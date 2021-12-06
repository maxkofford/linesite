<?php
namespace crud;
require_once (__DIR__ . "/../apptop.php");

$input = \core\Input::GetAll();

if (array_key_exists('module', $input) && strlen($input['module']) > 0 &&
        array_key_exists('module_input', $input) && strlen($input['module_input']) > 0 && 
        array_key_exists('display', $input) && strlen($input['display']) > 0) {          
    $module = $input['module'];
    $module_input = $input['module_input'];
    
    $display = $input['display'];

    $module = \core\crud\crud_module_manager::get_target_module($module);
   
    if($module !== false){
        $display = \core\crud\crud_module_manager::get_target_display($display);
        
        if($display !== false){
            
            if(array_key_exists('action', $input) && strlen($input['action']) > 0){
                if($input['action'] == 'update_single');
                $display->update_data($module, \core\Input::GetPOST());
            }
            
            $table = $display->echo_crud_multiple($module, $module_input);
            if(!$table){
                echo_no_found();
            }
        } else {
            echo_no_found();
        }  
    } else {
        echo_no_found();
    }
}
else {
    ?>
    <div class="h1">Welcome to Max's iron cookie!</div>
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