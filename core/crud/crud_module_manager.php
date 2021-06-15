<?php

namespace core\crud;

class crud_module_manager {
    public static function get_all_modules() {
        return [
                \core\crud\crud_modules\crud_module_dance_by_name::module_name => new \core\crud\crud_modules\crud_module_dance_by_name(),
        ];
    }
    
    public static function get_target_module($target){
        $modules = static::get_all_modules();
        if(array_key_exists($target, $modules)){
            return $modules[$target];
        } else {
            return false;
        }
    }
}