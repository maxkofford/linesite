<?php
namespace core\crud\crud_modules;

abstract class crud_module {
    public abstract function get_module_name();
    public abstract function get_table_name();
    public abstract function get_row_title_name();
    public abstract function get_column_types($data);
    public abstract function get_data_from_input($input);
    public function column_name_pre_process($data) {
        $output_data = [];
        $name_transform = $this->get_column_name_transform();
        $name_transform = array_flip($name_transform);
        foreach($data as $name => $value){
            if(array_key_exists($name, $name_transform)){
                $output_data[$name_transform[$name]] = $value;
            }
        }
        
        return $output_data;
    }   
    public function column_name_post_process($data) {
        $output_data = [];
        $name_transform = $this->get_column_name_transform();
        foreach($data as $name => $value){
            if(array_key_exists($name, $name_transform)){
                $output_data[$name_transform[$name]] = $value;
            } else {
                $output_data[$name] = $value;
            }
        }
        
        return $output_data;
    }
    
    public function column_html($data){    
        $typed_data = $this->get_column_types($data);
        $typed_data = $this->column_name_post_process($typed_data);
        $output_data = [];
        foreach($typed_data as $name => $value){
            if(is_object($value)){
                $output_data[$name] = $value->html();
            } else {
                $output_data[$name] = "<div name='".$name."'>".$value."</div>";
            }
        }
        return $output_data;
    }
    
    public function column_string($data){
        $typed_data = $this->get_column_types($data);
        $typed_data = $this->column_name_post_process($typed_data);
        $output_data = [];
        foreach($typed_data as $name => $value){
            if(is_object($value)){
                $output_data[$name] = $value->to_string();
            } else {
                $output_data[$name] = "<div name='".$name."'>".$value."</div>";
            }
        }
        return $output_data;
    }
    
    public function column_pre_process($data) {
        $fixed_name_data = $this->column_name_pre_process($data);
        $typed_data = $this->get_column_types($fixed_name_data);
        $output_data = [];
        foreach($typed_data as $name => $value){
            if(is_object($value)){
                $output_data[$name] = $value->pre_process();
            } else {
                $output_data[$name] = $value;
            }
        }
        return $output_data;
    }
    
    public function column_post_process($data) {
        $fixed_name_data = $this->column_name_post_process($data);
        $typed_data = $this->get_column_types($fixed_name_data);
        $output_data = [];
        foreach($typed_data as $name => $value){
            if(is_object($value)){
                $output_data[$name] = $value->post_process();
            } else {
                $output_data[$name] = $value;
            }
        }
        return $output_data;
    }
}