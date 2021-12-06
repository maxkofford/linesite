<?php
namespace core\crud\crud_types;

class crud_type_main_id_hidden extends crud_type {
    
    public function basic_html($value){
        return "<input class='crud_piece' type='text' style='display:none;' name='".$this->name."' value='".$value."' readonly>";
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