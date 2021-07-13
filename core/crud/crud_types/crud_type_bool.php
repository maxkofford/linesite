<?php
namespace core\crud\crud_types;

class crud_type_bool extends crud_type_string {
    
    public function __construct($name, $value){
        $this->name = $name;
        $this->value = $value;
    }
    
    public function html() {
        return parent::basic_html($this->post_process());
    }

    public function post_process() {
        if($this->value == 0){
            return "False";
        } else {
            return "True";
        }
    }

    public function pre_process() {
        if($this->value == "False"){
            return 0;
        } else {
            return 1;
        }
    }

    public function to_string(){
        return $this->post_process();
    }
}