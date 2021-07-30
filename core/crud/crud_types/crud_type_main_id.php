<?php
namespace core\crud\crud_types;

class crud_type_main_id extends crud_type {

    public function __construct($name, $value){
        $this->name = $name;
        $this->value = $value;
    }
    
    public function basic_html($value){
        return "<div name='".$this->name."'>".$value."</div>";
    }
    
    public function html() {
        return $this->basic_html($this->value);
    }
    
    public function pre_process() {
        return $this->value;
    }
    
    public function post_process() {
        return $this->value;
    }
    
    public function to_string(){
        return $this->post_process();
    }
    
}