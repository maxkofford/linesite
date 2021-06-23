<?php
namespace crud;
require_once (__DIR__ . "/../apptop.php");

$input = \core\Input::GetAll();

if (array_key_exists('module', $input) && strlen($input['module']) > 0 &&
        array_key_exists('module_input', $input) && strlen($input['module_input']) > 0) {
            
    $module = $input['module'];
    $module_input = $input['module_input'];
    
    $module = \core\crud\crud_module_manager::get_target_module($module);
    if($module !== false){
        //$table = \core\crud\crud_html_table::echo_crud_multiple($module,$module_input);
        $table = \core\crud\crud_html_accordian::echo_crud_multiple($module, $module_input);
        if(!$table){
            echo_no_found();
        }
    } else {
        echo_no_found();
    }
}
else {
    ?>
    <div class="h1">Welcome to the iron cookie!</div>
    <div class="row">
    <?php \core\HTML::echo_search_box() ?>
	</div>
    <?php
}

function echo_no_found(){
    ?>
	<div class="h1">No dances found!</div>
	Please try again.
	<div class="row">
	<?php \core\HTML::echo_search_box() ?>
	</div>
    <?php
}
?>
<?php require_once (__DIR__ . "/../appbottom.php"); ?>