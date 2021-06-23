<?php
namespace core\crud;

class crud_html_table {

    public static function echo_crud_multiple($module, $module_input){
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
                $typed_data = $module->get_column_types($data[0]);
                $typed_data = $module->column_name_post_process($typed_data);
                
                foreach($typed_data as $name => $value){
                    if(is_object($value) && strpos(get_class($value), "crud_type_hide") === false ){
                        ?>
                        <th><?php echo $name?></th>
                        <?php
                    }
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
                    if($value !== false){
                    ?>
                    <td>
                    <?php
                    echo str_replace("'", "", trim($value));
                    ?>
                    </td>
                    <?php  
                    }
                }
                ?>
                </tr>
                <?php 
            }      
            ?>
			</tbody>
		</table>
        <?php 
            return true;
        } else {
            return false;
        }
    }
}