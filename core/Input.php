<?php

namespace core;

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
        if (array_key_exists($input_name, $_POST)) {
            return $_POST[$input_name];
        }
        return $default_value;
    }
    
    public static function GetAll(){
        return array_merge($_GET,$_POST);
    }
    
    public static function GetPOST(){
        return $_POST;
    }
    
    public static function GetGET(){
        return $_GET;
    }
    
    public static function GetCookie($cookie_name, $default_value){
        if(array_key_exists($cookie_name, $_COOKIE)){
            return $_COOKIE[$cookie_name];
        }
        return $default_value;
    }
}