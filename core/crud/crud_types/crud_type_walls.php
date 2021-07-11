<?php
namespace core\crud\crud_types;

class crud_type_walls extends crud_type_string {
    
    public function __construct($name, $value){
        $this->name = $name;
        $this->value = $value;
    }
    
    public function html() {
        return parent::basic_html($this->post_process());
    }

    public function post_process() {
        if($this->value == -1){
            return "Circle";
        } else {
            return $this->value;
        }
    }

    public function pre_process() {
        if($this->value == "Circle"){
            return -1;
        } else {
            return $this->value;
        }
    }

    
}