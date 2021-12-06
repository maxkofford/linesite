<?php
namespace core\crud\crud_types;

class crud_type_link extends crud_type_string {
    
    public function basic_html($value){
        if(\core\Permissions::permission_level() == \core\Permissions::admin){
            return parent::basic_html($value);
        } else {
            if(strlen($this->value) > 0){
                return "<a class='crud_piece' data-name='". $this->name." 'href='".$value."' target='_blank'>". parent::basic_html($value)."</a>";                
            } else {
                return "";
            }
        }
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