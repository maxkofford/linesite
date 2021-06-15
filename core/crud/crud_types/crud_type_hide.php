<?php
namespace core\crud\crud_types;

class crud_type_hide extends crud_type {
    
    public function __construct($name, $value){
        $this->name = $name;
        $this->value = $value;
    }

    public function html() {
        return false;
    }

    public function pre_process() {
        return $this->value;
    }

    public function post_process() {
        return $this->value;
    }

    
}