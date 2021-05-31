<?php

namespace core;

class DBException extends \Exception {

    /** @var string */
    private $query;
    /** @var array */
    private $parameters;
    private $log_id;
    /**
     * a optional string error_tag to add extra info to a queries errors
     *
     * @var string
     */
    private $error_tag;

    function __construct($query, $parameters, $error_message, $error_tag = '') {
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

    public function getErrorTag() {
        return $this->error_tag;
    }
}