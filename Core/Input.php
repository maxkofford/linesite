<?php

namespace Core;

class Input {

    /**
     * Trys to get a input item
     *
     * @param string $input_name
     * @param int $default_value
     */
    public static function Get($input_name, $default_value) {
        if (array_key_exists($input_name, $_GET)) {
            return $_GET[$input_name];
        }
        return $default_value;
    }
    
    public static function GetAll(){
        return $_GET;
    }
}