<?php
namespace core\crud;

class crud_html_accordian {

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
		<div class="data-row">

            <?php
            $typed_data = $module->get_column_types($data[0]);
            $typed_data = $module->column_name_post_process($typed_data);
            
            foreach($typed_data as $name => $value){
                if(is_object($value) && strpos(get_class($value), "crud_type_hide") === false ){
                    ?>
                    <?php //echo $name?>
                    <?php
                }
            }
            ?>
               
            <?php
            $data_row_spot = 0;
            foreach($data as $data_row){
                $data_row_spot++;
                ?>
                <div class="card">
            		<div class="card-header" id="heading<?php echo $data_row_spot?>">
            			<h5 class="mb-0">
            				<button class="btn btn-link w-100" data-toggle="collapse"
            					data-target="#collapse<?php echo $data_row_spot?>" aria-expanded="<?php echo ($data_row_spot == 1) ? "true" : "false" ?>"
            					aria-controls="collapse<?php echo $data_row_spot?>">
                                <?php 
                                echo strlen($data_row[$module->get_row_title_name()]) > 0 ? $data_row[$module->get_row_title_name()] : $data_row[$module->get_table_name()."_id"];
                                ?>

            				</button>
            			</h5>
            		</div>

            		<div id="collapse<?php echo $data_row_spot?>" class="collapse <?php echo ($data_row_spot == 1) ? "show" : "" ?>"
            			aria-labelledby="heading<?php echo $data_row_spot?>" data-parent=".data-row">
            			<div class="card-body">
                        <?php 
                        $html_pieces = $module->column_html($data_row);
                        foreach($html_pieces as $name => $value){
                            if($value !== false){
                            ?>
                            <div class="row">
                            	<div class="col-6 text-right"><?php echo $name?>: </div>
                            	<div class="col-6">
                                <?php
                                echo trim($value);
                                ?>
                            	</div>
                            </div>
                            <?php  
                            }
                        }
                        ?>
                		</div>
            		</div>
                </div>
                <?php 
            }     
            ?>
			</div>
        <?php
            return true;
        } else {
            return false;
        }
    }
}