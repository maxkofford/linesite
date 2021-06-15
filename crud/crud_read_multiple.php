<?php
require_once (__DIR__ . "/../apptop.php");

$input = \core\Input::GetAll();

if (array_key_exists('module', $input) && strlen($input['module']) > 0 &&
        array_key_exists('module_input', $input) && strlen($input['module_input']) > 0) {
            
    $module = $input['module'];
    $module_input = $input['module_input'];
    
    $module = \core\crud\crud_module_manager::get_target_module($module);
    if($module !== false){
        ?>
        <style>
           
            thead th {
                border-right: 1px solid #fff;
            }
            table tbody:first-of-type tr:nth-child(2n), table tbody:first-of-type tr:nth-child(2n) th, #table_index tbody:nth-of-type(2n) tr, #table_index tbody:nth-of-type(2n) th {
                background: #dfdfdf;
            }
            table tr {
                text-align: left;
            }
            table caption, table th, table td {
                padding: .1em .3em;
                margin: .1em;
                vertical-align: middle;
                text-shadow: 0 1px 0 #fff;
            }
            
             
            table th {
                padding: .4em .5em;
                font-weight: bold;
                color: #000;
                background: #f3f3f3;
                background: linear-gradient(#fff, #ccc);
            }
        </style>
        <?php
        $data = $module->get_data_from_input($module_input);
        if(count($data) > 0){
        ?>
        <table class="m-2">      	
            <thead>
                <tr>
                <?php
                $column_names = array_keys($module->column_name_post_process($data[0]));
                foreach($column_names as $column_name){
                    ?>
                    <th><?php echo $column_name?></th>
                    <?php
                }
                ?>
                </tr>
            </thead>
			<tbody>
            <?php
            foreach($data as $data_row){
                ?>
                <tr>
                <?php 
                $html_pieces = $module->column_html($data_row);
                foreach($html_pieces as $value){
                    ?>
                    <td>
                    <?php echo $value?>
                    </td>
                    <?php  
                }
                ?>
                </tr>
                <?php 
            }      
            ?>
			</tbody>
		</table>
        <?php 
        } else {
            ?>
            No dances found!
            <?php
        }
    } else {
        //redirect to main page
    }
}
else {
    //redirect to main page
    \core\HTML::Redirect("/linesite/crud/crud_insert_name.php");
}
?>
<?php require_once (__DIR__ . "/../appbottom.php"); ?>