<?php
namespace core\crud\crud_types;

class crud_type_string extends crud_type {
    
    public function basic_html($value){
        if(\core\Permissions::permission_level() == \core\Permissions::admin){
            return "<div name='".$this->name."'><input class='editable crud_piece' name='".$this->name."' type='text' value='".$value."'></div>";
        } else {
            return "<div name='".$this->name."'>".$value."</div>";
        }
    }
    
    public function html() {
        return $this->basic_html($this->post_process());
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