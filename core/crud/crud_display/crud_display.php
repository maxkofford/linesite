<?php
namespace core\crud\crud_display;

abstract class crud_display {
    public abstract function get_display_name();
    public abstract function echo_crud_multiple(\core\crud\crud_modules\crud_module $module, $module_input);
    public abstract function update_data(\core\crud\crud_modules\crud_module $module, $input);
}