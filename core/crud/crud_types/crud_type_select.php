<?php
namespace core\crud\crud_types;

class crud_type_select extends crud_type_string {
    
    public function __construct($name, $value){
        $this->name = $name;
        $this->value = $value;
    }
    
    public function basic_html_select($options){
        if(\core\Permissions::permission_level() == \core\Permissions::admin){
            $full_html = "<select class='editable' name='".$this->name."'>";
            foreach($options as $value => $display){
                $full_html .= "<option ".($this->value == $value ? "selected" : "")." value='".$value."'>".$display."</option>";
            }
            $full_html .= "</select>";
            return $full_html;
        } else {
            return parent::basic_html($this->post_process());
        }
    }
    
    public function html() {
        return parent::basic_html($this->post_process());
    }
    
    public function pre_process() {
        return $this->value;
    }
    
    public function post_process() {
        return $this->value;
    }
    
    
    public function select_post_process($options) {
        if(array_key_exists($this->value, $options)){
            return $options[$this->value];
        } else {
            return $options[0];
        }
    }
    
    public function select_pre_process($options) {
        $options = array_flip($options);
        if(array_key_exists($this->value, $options)){
            return $options[$this->value];
        } else {
            foreach($options as $value) {
                return $value;
            }
        }
    }
    
    public function to_string(){
        return $this->post_process();
    }
}