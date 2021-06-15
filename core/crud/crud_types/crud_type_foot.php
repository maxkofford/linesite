<?php
namespace core\crud\crud_types;

class crud_type_foot extends crud_type_string {
    
    public function __construct($name, $value){
        $this->name = $name;
        $this->value = $value;
    }
    
    public function html() {
        return parent::basic_html($this->post_process());
    }

    public function post_process() {
        if($this->value == 0){
            return "Left";
        } else {
            return "Right";
        }
    }

    public function pre_process() {
        if($this->value == "Left"){
            return 0;
        } else {
            return 1;
        }
    }

    
}