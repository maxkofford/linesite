<?php
require_once (__DIR__ . "/../apptop.php");

$input = \core\Input::GetAll();

if (array_key_exists('module', $input) && strlen($input['module']) > 0 &&
        array_key_exists('table_key', $input) && strlen($input['table_key']) > 0) {
    $table_name = $input['table_name'];
    $table_key = $input['table_key'];
    $data = \Core\DB::execute("SELECT * FROM " . $table_name . " WHERE " . $table_name . "_id = :table_key", ['table_key' => $table_key] );
    foreach($data as $one_row){
        
    }
}
else {
    
    \core\HTML::Redirect("/linesite/crud/crud_insert_name.php");
}
?>