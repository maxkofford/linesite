<?php
namespace core\crud\crud_types;

abstract class crud_type {
    
    public function __construct($name, $value){
        $this->name = $name;
        $this->value = $value;
    }
    
    public function set_name($name){
        $this->name = $name;
    }
    
    public abstract function pre_process();
    public abstract function post_process();
    public abstract function html();
    public abstract function to_string();
    
    
}