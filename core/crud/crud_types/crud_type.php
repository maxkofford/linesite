<?php
namespace core\crud\crud_types;

abstract class crud_type {
    public abstract function pre_process();
    public abstract function post_process();
    public abstract function html();
    
}