<?php
namespace core\crud\crud_types;

class crud_type_foot extends crud_type_select {
    
    public function __construct($name, $value){
        $this->name = $name;
        $this->value = $value;
    }
    
    public function get_select_options(){
        return ["Left",
                "Right"];
    }
    
    public function html() {
        return parent::basic_html_select($this->get_select_options());
    }
    
    public function post_process() {
        return parent::select_post_process($this->get_select_options());
    }
    
    public function pre_process() {
        return parent::select_pre_process($this->get_select_options());
    }
    
    public function to_string(){
        return $this->post_process();
    }
    
}