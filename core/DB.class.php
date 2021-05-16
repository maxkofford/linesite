<?php
/**
 * Created by PhpStorm.
 * User: matthew.zahm
 * Date: 10/27/2016
 * Time: 3:15 PM
 */

namespace Core;

class DBException extends \Exception
{

    /** @var string */
    private $query;
    /** @var array */
    private $parameters;
    private $log_id;
    /**
     * a optional string error_tag to add extra info to a queries errors
     * @var string
     */
    private $error_tag;

    function __construct($query, $parameters, $error_message, $error_tag = '')
    {
        parent::__construct($error_message);

        $this->query = $query;
        $this->parameters = $parameters;
        $this->error_tag = $error_tag;     
    }
    
    public function setLogId($log_id) {
        $this->message = "(Log Id: {$log_id}) - {$this->message}";
        $this->log_id = $log_id;
    }

    public function getLogId() {
        return $this->log_id;
    }

    public function getQuery() {
        return $this->query;
    }

    public function getParameters() {
        return $this->parameters;
    }
    
    public function getErrorTag(){
        return $this->error_tag;
    }

}

class Connection_Type {

    const WRITE = 0;  // Master
    const READ = 1;
    const REPORTING = 2;

    public static function get_connection_details($connection_type = self::WRITE) {
        switch($connection_type) {
            case self::WRITE:
                $server = DB_SERVER;
                $database = DB_DATABASE;
                $username = DB_SERVER_USERNAME;
                $password = DB_SERVER_PASSWORD;
                break;
            case self::READ:
                $server = DB_SERVER2;
                $database = DB_DATABASE2;
                $username = DB_SERVER_USERNAME2;
                $password = DB_SERVER_PASSWORD2;
                break;
            case self::REPORTING:
                $server = DB_REPORT_SERVER;
                $database = DB_REPORT_DATABASE;
                $username = DB_REPORT_USERNAME;
                $password = DB_REPORT_PASSWORD;
                break;
            default:
                throw new \Exception("{$connection_type} is not a valid connection type");
                break;
        }

        return [
            'dsn' => "mysql:host=".$server.";dbname=".$database,
            'user' => $username,
            'password' => $password
        ];
    }

}

class DB
{
    private static $previous_write_override_value = false;

    protected static $instance_cache = [];
    private static $write_override = false;

    private static $connection_attempts = 0;
    private static $max_connection_attempts = 3;
    private static $can_retry = true;

    protected function __construct() {}
    protected function __clone() {}

    /**
     * Handles static methods that don't exist on this object by passing them onto the PDO instance.
     *
     * @param string $method    What (non-existent) method was called.
     * @param array $args       The arguments passed to the method,
     * @return mixed            What ever the method would normally return.
     */
    public static function __callStatic($method, $args)
    {
        // Always use the write/master instance, I don't think there's a good way of letting them pass the type through.
        return call_user_func_array(array(self::instance(), $method), $args);
    }

    /**
     * Sets the write_override setting, which forces ::execute and ::query to use the Master/Write connection.
     *
     * @param bool $override    Whether or not to always use Master/Write
     */
    public static function set_write_override($override) {
        self::$write_override = $override;
    }

    public static function get_write_override() {
        return self::$write_override;
    }

    /**
     * Manages the internal PDO object/connection.
     *
     * @param int $connection_type  See Connection_Type above for valid values.
     * @param array $options        Options to pass to the PDO constructor
     * @return \PDO|null
     */
    public static function instance($connection_type = Connection_Type::WRITE, $options = array())
    {
        $is_invalid_connection_type = $connection_type !== Connection_Type::WRITE
            && $connection_type !== Connection_Type::READ
            && $connection_type !== Connection_Type::REPORTING;
        if($is_invalid_connection_type) {
            $connection_type = Connection_Type::WRITE;
        }

        if(!array_key_exists($connection_type, self::$instance_cache) || self::$instance_cache[$connection_type] === null) {
            self::$instance_cache[$connection_type] = self::get_pdo($connection_type, $options);
        }

        return self::$instance_cache[$connection_type];
    }

    /**
     * Gets a PDO object using the global DB settings, connected either to the main DB or to reporting.
     *
     * @param int $connection_type  See Connection_Type above for valid values.
     * @param array $options        Any additional options.
     * @return null|\PDO            The PDO object
     *
     * @throws \Core\DBException
     */
    public static function get_pdo($connection_type = Connection_Type::WRITE, $options = array()){
        $is_invalid_connection_type = $connection_type !== Connection_Type::WRITE
            && $connection_type !== Connection_Type::READ
            && $connection_type !== Connection_Type::REPORTING;
        if($is_invalid_connection_type) {
            $connection_type = Connection_Type::WRITE;
        }

        $pdo = null;

        try {
            $connection_details = Connection_Type::get_connection_details($connection_type);
            $pdo = new \PDO(
                $connection_details['dsn'],
                $connection_details['user'],
                $connection_details['password'],
                $options
            );
        }
        catch(\Exception $exception) {
            if(php_sapi_name() !== "cli"){
                // Can't really do anything without a DB connection, so kill the page. Same thing as tep does on failure.
                header('HTTP/1.1 503 Service Temporarily Unavailable');
                header('Status: 503 Service Temporarily Unavailable');
                header('Retry-After: 7200'); // in seconds}
                die("We're sorry for the inconvenience but our server is experiencing high load, please try again.");
            } else {
                throw $exception;
            }    
        }

        return $pdo;
    }

    /**
     * Throw out the current connection and re-connect.
     *
     * @param int $connection_type
     */
    private static function reconnect($connection_type = Connection_Type::WRITE) {
        unset(self::$instance_cache[$connection_type]);
        self::$instance_cache[$connection_type] = self::get_pdo($connection_type);

        self::$connection_attempts++;
        self::$can_retry = self::$connection_attempts < self::$max_connection_attempts;
    }

    /**
     * Start a transaction on the current PDO write instance.
     * Always use the write instance for transactions
     */
    public static function begin_transaction() {
        static::$previous_write_override_value = static::get_write_override();
        static::$write_override = true;
        static::instance(Connection_Type::WRITE)->beginTransaction();
    }

    /**
     * Rollback a transaction on the current PDO write instance.
     * Always use the write instance for transactions
     */
    public static function rollback() {
        static::$write_override = static::$previous_write_override_value;
        static::instance(Connection_Type::WRITE)->rollback();
    }

    /**
     * Commit a transaction on the current PDO write instance.
     * Always use the write instance for transactions
     */
    public static function commit() {
        static::$write_override = static::$previous_write_override_value;
        static::instance(Connection_Type::WRITE)->commit();
    }

    /**
     * Run a MySQL statement and return the prepared statement so you can iterate on it,
     * pull single results back, or anything else you could do with PDOs.
     *
     * @param string $sql           The query for the prepared statement
     * @param array $parameters     The parameters for the prepared statement
     * @param bool $use_reporting   Whether or not to use the reporting server
     * @param bool $transactional   Whether or not this is a locking transactional query.
     *                              use case: SELECT FOR UPDATE, to hold records until they are updated.
     * @param string $error_tag     A optional tag to add to errors for this query       
     * @return mixed                The prepared statement from the query
     *
     * @throws \Core\DBException
     */
    public static function query($sql, $parameters = array(), $use_reporting = false, $transactional = false, $error_tag = '')
    {
        $connection_type = Connection_Type::WRITE;
        if(strpos(strtolower(trim($sql)), "select") === 0) {
            // Force select queries onto the read connection
            $connection_type = Connection_Type::READ;
        }

        if($transactional || self::$write_override) {
            // Transactions are done on the Write connection, even if they are selects
            // We also use master/write for critical page selects, based on $write_override
            $connection_type = Connection_Type::WRITE;
        }
        else if($use_reporting) {
            $connection_type = Connection_Type::REPORTING;
        }

        $query = self::instance($connection_type)->prepare($sql);

        $normalized_parameters = [];
        foreach($parameters as $field => $value) {
            $parameter_type = \PDO::PARAM_STR;
            $parameter_value = $value;
            if(is_array($value)) {
                $parameter_type = $value['type'];
                $parameter_value = $value['value'];
            }

            $field_name = (strpos($field, ':') !== false ? $field : ':' . $field);
            $normalized_parameters[$field_name] = [
                'type' => $parameter_type,
                'value' => $parameter_value
            ];
        }

        // ... this may seem pointless, why not just do it in the above loop? you might ask
        // bindParam takes the value as a reference, so using $parameter_value above breaks things terribly
        // since it's value changes in each iteration of the loop, thus changing every single bound value to the new one.
        foreach($normalized_parameters as $field_name => $parameter) {
            $query->bindParam($field_name, $parameter['value'], $parameter['type']);
        }

        $query->execute();

        $needs_reconnect = self::check_for_error($sql, $parameters, $query, $error_tag);
        if($needs_reconnect) {
            self::reconnect($connection_type);
            if(self::$can_retry) {
                $query = self::query($sql, $parameters, $use_reporting, $transactional, $error_tag);
            }
        }

        return $query;
    }

    /**
     * Executes the select $sql and returns an array of associative arrays or,
     * if $object is defined, then an array of objects.
     *
     * @param string $sql           The select query string that you wish to execute.
     * @param array $parameters     The parameters used in the $sql query.
     * @param string|bool $object   What object class you'd like the results returned ad.
     * @param bool $use_reporting   Whether or not to use the reporting server.
     * @param bool $transactional   Whether or not this is a locking transactional query.
     *                              use case: SELECT FOR UPDATE, to hold records until they are updated.
     * @param string $error_tag     A optional tag to add to errors for this query   
     * @return array                The array of items that the query return either in array or object form.
     *
     * @throws \Core\DBException    Thrown when anything goes wrong with the query
     */
    public static function execute($sql, $parameters = array(), $object = "", $use_reporting = false, $transactional = false, $error_tag = ''){
        // These must be SELECT statements, anything else raises a warning and pushes the request to ::run.
        if(strpos(strtolower(trim($sql)), "select") !== 0) {
            $warning = "Core\DB Warning: Attempting to run non-SELECT on the read instance. Use ::run for non-SELECT queries. -- Request pushed to ::run";
            trigger_error($warning, E_USER_WARNING);
            return self::run($sql, $parameters, $error_tag);
        }

        $connection_type = Connection_Type::READ;
        if($transactional || self::$write_override) {
            // Transactions are done on the Write connection
            // We also use master/write for critical page selects, based on $write_override
            $connection_type = Connection_Type::WRITE;
        }
        else if($use_reporting) {
            $connection_type = Connection_Type::REPORTING;
        }

        $pdo = self::instance($connection_type);
        $items = array();

        // Passing execute the $parameters array directly defaults all types to PARAM_STR,
        // when using named parameters for things like LIMIT we need control of the type
        // so we'll bind the parameters manually instead of letting execute do it.
        $normalized_parameters = [];
        $unpacked_parameters = [];
        foreach($parameters as $field => $parameter) {
            if(is_object($parameter)) {
                $class_given = get_class($parameter);
                $error_message = "The query parameter must be an array or basic type, {$class_given} given.";

                $db_exception = new DBException($sql, $parameters, $error_message, $error_tag);
                self::log_error($db_exception);

                throw $db_exception;
            }
            else if(is_array($parameter)) {
                $array_value = null;
                $array_type = null;
                if(!array_key_exists('value', $parameter)) {
                    $array_value = $parameter;
                    $array_type = \PDO::PARAM_STR;
                }
                else if (is_array($parameter['value'])) {
                    $array_value = $parameter['value'];
                    $array_type = $parameter['type'];
                }

                if($array_value !== null) {
                    // This is an array parameter so we need to unpack it.
                    try {
                        $new_query = self::unpack_array($field, $sql, $array_value, $array_type);
                    }
                    catch(\Exception $exception) {
                        // Convert the thrown exception to a DB exception with query info.
                        $db_exception = new DBException($sql, $parameters, $exception->getMessage(), $error_tag);
                        self::log_error($db_exception);

                        throw $db_exception;
                    }
                    $sql = $new_query['sql'];
                    $unpacked_parameters = array_merge($unpacked_parameters, $new_query['unpacked_parameters']);
                }
                else {
                    // It's just a regular parameter
                    $normalized_parameters[$field] = $parameter;
                }
            }
            else {
                $normalized_parameters[$field] = array('value' => $parameter, 'type' => \PDO::PARAM_STR);
            }
        }

        // Unpack array will alter the $sql, so we need to prepare down here, and then attach the parameters.
        $execute_query = $pdo->prepare($sql);

        // Two loops is more efficient than merging and still looping.
        foreach($normalized_parameters as $field => $parameter) {
            $execute_query->bindParam((strpos($field, ':') !== false ? $field : ':' . $field), $parameter['value'], $parameter['type']);
        }
        foreach($unpacked_parameters as $field => $parameter) {
            $execute_query->bindParam((strpos($field, ':') !== false ? $field : ':' . $field), $parameter['value'], $parameter['type']);
        }

        try {
            $execute_query->execute();
        }
        catch (\Exception $exception) {
            // Intercept the PDO exception and log is as our own DBException so we get better query info
            $db_exception = new DBException($sql, $parameters, $exception->getMessage(), $error_tag);
            self::log_error($db_exception);

            throw $db_exception;
        }

        $needs_reconnect = self::check_for_error($sql, $parameters, $execute_query, $error_tag);
        if($needs_reconnect) {
            self::reconnect($connection_type);
            if(self::$can_retry) {
                $items = self::execute($sql, $parameters, $object, $use_reporting, $transactional, $error_tag);
            }
        }

        // $items may have been populated by the recursive call on reconnect attempts.
        if(!$items) {
            if($object === true) {
                $items = $execute_query->fetchAll(\PDO::FETCH_OBJ);
            } elseif ($object) {
                $items = $execute_query->fetchAll(\PDO::FETCH_CLASS, $object);
            } else {
                $items = $execute_query->fetchAll(\PDO::FETCH_ASSOC);
            }
        }

        return $items;
    }

    /**
     * Take an array parameter, field = :field with 'field' => [value1, value2, ...], and unpack it into a series of
     * bound parameters, field = (:field_1, :field_2, ...) with 'field_1' => value1, 'field_2' => value2, ...
     *
     * @param string $field                 Which field we're binding the array to
     * @param string $sql                   The query we're doing the bindings on
     * @param array $array                  The values that we want bound to individual parameters
     * @param \PDO::value_type $array_type  \PDO::PARAM_STR, \PDO::PARAM_INT, ect
     *
     * @return array['sql']                 The altered query with parameterized array
     * @return array['unpacked_parameters'] The unpacked parameters, one per array value
     *
     * @throws \Exception                   When the parameters are invalid, caught and handled in ::execute
     */
    private static function unpack_array($field, $sql, $array, $array_type) {
        // Normalize $field, it may or may not have ":" so just make sure it always does.
        $field = ":" . str_replace(":", "", $field);

        $array_field = "(";
        $unpacked_parameters = [];

        foreach($array as $index => $value) {
            $parameter_field = $field . "_" . $index;
            $array_field .= $parameter_field . ", ";

            if(is_object($value)) {
                $class_given = get_class($value);
                throw new \Exception("The value in the query array-parameter must be a basic type, {$class_given} given.");
            }
            else if(is_array($value)) {
                throw new \Exception("The value in the query array-parameter must be a basic type, Array given.");
            }

            $unpacked_parameters[$parameter_field] = [
                'value' => $value,
                'type'  => $array_type
            ];
        }

        $array_field = rtrim($array_field, ", ") . ")";

        $sql = str_replace($field, $array_field, $sql);

        return [
          'sql' => $sql,
          'unpacked_parameters'  => $unpacked_parameters
        ];
    }


    /**
     * Just another way of running a select on the reporting server.
     *
     * @param string $sql           The select query string that you wish to execute.
     * @param array $parameters     The parameters used in the $sql query.
     * @param string $object        What object class you'd like the results returned ad.
     * @return array|bool           The array of items that the query return either in array or object form.
     *
     * @throws \Core\DBException
     */
    public static function execute_reporting($sql, $parameters = array(), $object = ""){
        return self::execute($sql, $parameters, $object, true);
    }

    /**
     * Runs the $sql and returns the number of rows effected.
     * If it is non-zero then your query worked, otherwise it did not
     *
     * @param string $sql           The select query string that you wish to execute.
     * @param array $parameters     The parameters used in the $sql query.
     * @param string $error_tag     A optional tag to add to errors for this query
     * @return int                  The number of rows effected.
     *
     * @throws \Core\DBException
     */
    public static function run($sql, $parameters = array(), $error_tag = ''){
        $row_count = false;

        // These cannot be SELECT statements, SELECTs raises a warning and pushes the request to ::execute.
        if(strpos(strtolower(trim($sql)), "select") === 0) {
            $warning = "Core\DB Warning: Attempting to run SELECT on the write instance. Use ::execute or ::query for SELECT queries. -- Request pushed to ::execute";
            trigger_error($warning, E_USER_WARNING);
            return self::execute($sql, $parameters, '', false, false, $error_tag);
        }

        $pdo = self::instance(Connection_Type::WRITE);
        
        // Passing run the $parameters array directly defaults all types to PARAM_STR,
        // when using named parameters for things like LIMIT we need control of the type
        // so we'll bind the parameters manually instead of letting run do it.
        $normalized_parameters = [];
        $unpacked_parameters = [];
        foreach($parameters as $field => $parameter) {
            if(is_object($parameter)) {
                $class_given = get_class($parameter);
                $error_message = "The query parameter must be an array or basic type, {$class_given} given.";
                
                $db_exception = new DBException($sql, $parameters, $error_message);
                self::log_error($db_exception);
                
                throw $db_exception;
            }
            else if(is_array($parameter)) {
                $array_value = null;
                $array_type = null;
                if(!array_key_exists('value', $parameter)) {
                    $array_value = $parameter;
                    $array_type = \PDO::PARAM_STR;
                }
                else if (is_array($parameter['value'])) {
                    $array_value = $parameter['value'];
                    $array_type = $parameter['type'];
                }
                
                if($array_value !== null) {
                    // This is an array parameter so we need to unpack it.
                    try {
                        $new_query = self::unpack_array($field, $sql, $array_value, $array_type);
                    }
                    catch(\Exception $exception) {
                        // Convert the thrown exception to a DB exception with query info.
                        $db_exception = new DBException($sql, $parameters, $exception->getMessage(), $error_tag);
                        self::log_error($db_exception);
                        
                        throw $db_exception;
                    }
                    $sql = $new_query['sql'];
                    $unpacked_parameters = array_merge($unpacked_parameters, $new_query['unpacked_parameters']);
                }
                else {
                    // It's just a regular parameter
                    $normalized_parameters[$field] = $parameter;
                }
            }
            else {
                $normalized_parameters[$field] = array('value' => $parameter, 'type' => \PDO::PARAM_STR);
            }
        }
        
        // Unpack array will alter the $sql, so we need to prepare down here, and then attach the parameters.
        $run_query = $pdo->prepare($sql);
        
        // Two loops is more efficient than merging and still looping.
        foreach($normalized_parameters as $field => $parameter) {
            $run_query->bindParam((strpos($field, ':') !== false ? $field : ':' . $field), $parameter['value'], $parameter['type']);
        }
        foreach($unpacked_parameters as $field => $parameter) {
            $run_query->bindParam((strpos($field, ':') !== false ? $field : ':' . $field), $parameter['value'], $parameter['type']);
        }
        
        try {
            $run_query->execute();
        }
        catch (\Exception $exception) {
            // Intercept the PDO exception and log is as our own DBException so we get better query info
            $db_exception = new DBException($sql, $parameters, $exception->getMessage(), $error_tag);
            self::log_error($db_exception);

            throw $db_exception;
        }

        $needs_reconnect = self::check_for_error($sql, $parameters, $run_query, $error_tag);
        if($needs_reconnect) {
            self::reconnect(Connection_Type::WRITE);
            if(self::$can_retry) {
                $row_count = self::run($sql, $parameters, $error_tag);
            }
        }

        return $row_count !== false ? $row_count : $run_query->rowCount();
    }
    
    /**
     *  Same as run except batches the first found array parameter into groups with delay between groups
     *  eg   sql= "DELETE FROM eldis_api_log_archives WHERE id IN :id_list" with $parameters = ['id_list'=>[1,2,3]]
     * @param string $sql 
     * @param array $parameters same as run except the first array parameter is batched
     * @param int $batch_size maximum batch size
     * @param int $second_delay_between_batches seconds of delay between batches so db logs dont build up too quickly
     */
    public static function batch_run_array($sql, $parameters = array(), $batch_size = 1000, $second_delay_between_batches = 10){
     
        $batch_array = [];
        $found_key = '';
        foreach($parameters as $param_name => $param_value){
            if(is_array($param_value)){
                $batch_array = $param_value;
                $found_key = $param_name;
                break;
            }
        }
  
        if(count($batch_array) < 1){
            return 0;
        }
        
        unset($parameters[$found_key]);
        
        $rows_affected = 0;
        $current_spot = 0;
        while($current_spot < count($batch_array)) {
            $batch_param = [$found_key => array_slice($batch_array, $current_spot, $batch_size)];
            $rows_affected += \Core\DB::run($sql, array_merge($parameters, $batch_param));
            $current_spot += $batch_size;
            
            // Need to sleep between sets so that the db logs don't build up too quickly.
            sleep($second_delay_between_batches);
        }
        
        return $rows_affected;
    }

    /**
     * Runs an insert command and returns the last inserted Id.
     * Will return false for everything except an insert.
     *
     * @param string $sql           The select query string that you wish to execute.
     * @param array $parameters     The parameters used in the $sql query.
     * @return bool|string          The id of the last thing inserted.
     *
     * @throws \Core\DBException
     */
    public static function insert_and_get_id($sql, $parameters = array()){
        $return_id = false;

        if(strpos(strtolower(trim($sql)), 'insert') === 0){
            $pdo = self::instance(Connection_Type::WRITE);
            $run_query = $pdo->prepare($sql);
            $run_query->execute($parameters);

            $needs_reconnect = self::check_for_error($sql, $parameters, $run_query);
            if($needs_reconnect) {
                self::reconnect(Connection_Type::WRITE);
                if(self::$can_retry) {
                    $return_id = self::insert_and_get_id($sql, $parameters);
                }
            }

            if($return_id === false && $run_query->rowCount() > 0){
                $return_id = $pdo->lastInsertId();
            }
        }

        return $return_id;
    }

    /**
     * Insert all items in the data array into the table (NOT INJECTION SAFE!!).
     *
     * Example
     *    $now = date("Y-m-d H:i:s");
     *
     *    // These keys/object property names must match those in the $columns array
     *    $data = [
     *      [
     *          'timestamp' => $now,
     *          'message' => "Insert Unit Tests 1",
     *          'trace' => "Insert Unit Tests 1",
     *          'query' => "Insert Unit Tests 1",
     *          'parameters' => "Insert Unit Tests 1"
     *      ],
     *      [
     *          'timestamp' => $now,
     *          'message' => "Insert Unit Tests 2",
     *          'trace' => "Insert Unit Tests 2",
     *          'query' => "Insert Unit Tests 2",
     *          'parameters' => "Insert Unit Tests 2"
     *      ]
     *    ];
     *
     *    // These must match the keys/object property names passed in as the $data.
     *    $columns = [
     *      "timestamp", "message", "trace", "query", "parameters"
     *    ];
     *
     *    $table = "core_db_errors";
     *
     *    $rows_inserted = DB::batch_insert($data, $columns, $table);
     *
     * @param array/object $data_array  The array of object (or associative arrays) that we want to insert
     * @param array $columns            Array of columns that we will insert
     * @param string $table             The table we want to insert into
     * @param bool $ignore_duplicates   Whether to ignore duplicates or not
     *
     * @return bool                     Whether or not the insert was successful
     */
    public static function batch_insert($data_array, $columns, $table, $ignore_duplicates = false) {
        $insert_success = false;

        if(count($data_array) > 0) {
            $columns_string = implode(",", $columns);

            $ignore = "";
            if($ignore_duplicates) {
                $ignore = "IGNORE";
            }

            $insert_query = "
                INSERT {$ignore} INTO 
                  {$table} ({$columns_string})
                VALUES
            ";

            foreach($data_array as $item) {
                $values_string = "(";

                $object_string = "";
                foreach($columns as $column) {
                    $escaped_value = str_replace("'", "\'", $item[$column]);
                    // The above might escape an already escaped quote, "\'" => "\\'" so fix those next.
                    $escaped_value = str_replace("\\\\'", "\'", $escaped_value);

                    $object_string .= "'" . $escaped_value . "',";
                }
                $values_string .= rtrim($object_string, ",");

                $values_string .= "),";

                $insert_query .= $values_string;
            }

            $insert_query = rtrim($insert_query, ',') . ";";

            $insert_success = static::run($insert_query);
        }

        return $insert_success;
    }


    /**
     * Same as batch_insert but is injection safe (assuming column names and table name are not user input), doesnt require columns array, and has update_duplicates in addition to ignore duplicates
     * Assumes all data array's have the same columns in the same spots and all columns are the ones in the first data array
     *  $now = date("Y-m-d H:i:s");
     *
     *    $data_array = [
     *      [
     *          'timestamp' => $now,
     *          'message' => "Insert Unit Tests 1",
     *          'trace' => "Insert Unit Tests 1",
     *          'query' => "Insert Unit Tests 1",
     *          'parameters' => "Insert Unit Tests 1"
     *      ],
     *      [
     *          'timestamp' => $now,
     *          'message' => "Insert Unit Tests 2",
     *          'trace' => "Insert Unit Tests 2",
     *          'query' => "Insert Unit Tests 2",
     *          'parameters' => "Insert Unit Tests 2"
     *      ]
     *    ];
     *    
     * $collision_mode = 0 is regular error on collision
     * $collision_mode = 1 is ignore error on collision
     * $collision_mode = 2 is update row on collision
     *
     * @param array $data_array
     * @param string $table
     * @param number $collision_mode
     *            0 is regular error on collision, 1 is ignore error on collision, 2 is update row on collision
     * @param number $biggest_batch_size How many rows to insert in each batch size
     * @return boolean|number
     */
    public static function batch_insert2($data_array, $table, $collision_mode = 0, $biggest_batch_size = 1000) {
        $total_changed = 0;
        $batched_data_array = array_chunk($data_array, $biggest_batch_size);
        foreach($batched_data_array as $current_data_array){     
            $current_changed = static::batch_insert2_helper($current_data_array, $table, $collision_mode);
            if($current_changed > 0){
                $total_changed += $current_changed;
            }
        }
        return $total_changed;
    }
    
    /**
     * Inserts one batch of values for batch_insert2
     * @return boolean|number
     */
    private static function batch_insert2_helper($data_array, $table, $collision_mode){
        $insert_success = false;
        $ignore_duplicates = false;
        $update_duplicates = false;
        
        if (count($data_array) > 0) {
            switch ($collision_mode) {
                case 1:
                    $ignore_duplicates = true;
                case 2:
                    $update_duplicates = true;
            }
            
            $keys = $data_array[0];
            if(!is_array($keys)) {
                $keys = get_object_vars($keys);
            }
            $columns = array_keys($keys);
            $columns_string = implode(',', $columns);
            
            $ignore = "";
            if ($ignore_duplicates) {
                $ignore = "IGNORE";
            }
            
            $update = "";
            if ($update_duplicates) {
                $update = " ON DUPLICATE KEY UPDATE ";
                foreach ($columns as $column) {
                    $update .= "{$column} = VALUES({$column}),";
                }
                $update = rtrim($update, ",");
            }
            
            $insert_query = "
                INSERT {$ignore} INTO
                  {$table} ({$columns_string})
                VALUES
            ";
                  
            $params = [];
            $row_spot = 0;
            foreach ($data_array as $row) {
                $values_string = "(";
                $object_string = "";
                $col_spot = 0;
                foreach ($row as $column => $item) {
                    $value_id = ':r' . $row_spot . 'c' . $col_spot;
                    $object_string .= "{$value_id},";
                    $params[$value_id] = $item;
                    $col_spot++;
                }
                
                $values_string .= rtrim($object_string, ",");
                
                $values_string .= "),";
                
                $insert_query .= $values_string;
                $row_spot++;
            }
            
            $insert_query = rtrim($insert_query, ',');
            $insert_query .= $update;
            
            $insert_success = static::run($insert_query, $params);
        }
        
        return $insert_success;
    }


    /**
     * We don't want the PDO to error silently so make it throw an exception when an error occurs.
     *
     * @param string $sql                           The select query string that you wish to execute.
     * @param array $parameters                     The parameters used in the $sql query.
     * @param \PDOStatement|bool $prepared_query    The PDO to check errors on.
     * @param string $error_tag                     A optional tag to add to errors for this query
     *
     * @return boolean              Whether or not to re-run the query.
     * @throws \Core\DBException
     */
    private static function check_for_error($sql, $parameters, $prepared_query, $error_tag = '') {
        $needs_reconnect = false;
        $error_class = substr($prepared_query->errorCode(), 0, 2);

        // Hydration errors happen before actual execution, they deal with parameter binding issues.
        $hydration_error = $error_class == "HY";

        // Error class '00' is a success, '01' is a warning, and anything larger are actual errors.
        $log_error = $error_class > 0 || $hydration_error;
        $throw_exception = $error_class > 1 || $hydration_error;

        if ($log_error) {
            $error = $prepared_query->errorInfo();
            $message = $error[0] . ": " . $error[2];
            if($hydration_error && !$error[2]) {
                $message = $error[0] . ": Hydration Error - check your parameter binding for mis-matched parameters and :placeholders.";
            }

            if($error[0] == "HY000" && $error[2] == "MySQL server has gone away") {
                $needs_reconnect = true;
            }
            else {
                $exception = new DBException($sql, $parameters, $message, $error_tag);
                self::log_error($exception);

                if ($throw_exception) {
                    throw $exception;
                }
            }
        }

        return $needs_reconnect;
    }

    /**
     * Logs all pertinent information about the SQL error to the core_db_errors table.
     *
     * @param \Core\DBException $exception  Everything important is in this object
     */
    public static function log_error(DBException &$exception) {
        $log_query = "
            INSERT INTO
              core_db_errors (message, trace, query, parameters, error_tag)
            VALUES
              (:message, :trace, :query, :parameters, :error_tag);
        ";

        // $exception->getTrace() was behaving funny and never evaluating (just hangs) so switched this to the trace string.
        // getTraceAsString() leaves off the first error line, it's PHP so of course it does, so we have to add it manually.
        $trace = "#start {$exception->getFile()}({$exception->getLine()})\n{$exception->getTraceAsString()}";

        $log_parameters = array(
            'message' => $exception->getMessage(),
            'trace' => $trace,
            'query' => $exception->getQuery(),
            'parameters' => print_r($exception->getParameters(), TRUE),
            'error_tag' => $exception->getErrorTag()
        );

        // We don't want to get into an error logging loop by calling self:run and having that also
        // error so we'll execute it manually here on a separate pdo.
        $pdo = self::get_pdo(Connection_Type::WRITE);
        $run_query = $pdo->prepare($log_query);
        $run_query->execute($log_parameters);
        $log_id = $pdo->lastInsertId();

        $exception->setLogId($log_id);
    }

}