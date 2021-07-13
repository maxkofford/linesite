<?php
namespace core\crud\crud_types;

class crud_type_youtube extends crud_type_link {
    
    public function __construct($name, $value){
        $this->name = $name;
        $this->value = $value;
    }
    
    public function html() {
        return parent::basic_html($this->post_process());
    }

    public function pre_process() {
        $value = $this->value;
        if(strpos($this->value, "youtube") !== false){
            $value = str_replace("https://www.youtube.com/watch?v=", "", $this->value);
        }
        return $value;
    }

    public function post_process() {
        if(strlen($this->value) > 0){
            return "https://www.youtube.com/watch?v=" . $this->value;
        } else {
            return "";
        }
    }
    
    public function to_string(){
        return $this->post_process();
    }

    
}