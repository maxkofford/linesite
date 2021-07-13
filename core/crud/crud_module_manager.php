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
    
    public static function get_all_displays() {
        return [
                \core\crud\crud_display\crud_csv_generator::display_name => new \core\crud\crud_display\crud_csv_generator(),
                \core\crud\crud_display\crud_html_accordian::display_name => new \core\crud\crud_display\crud_html_accordian(),
                \core\crud\crud_display\crud_html_table::display_name => new \core\crud\crud_display\crud_html_table(),
        ];
    }
    
    public static function get_target_display($target){
        $modules = static::get_all_displays();
        if(array_key_exists($target, $modules)){
            return $modules[$target];
        } else {
            return false;
        }
    }
}