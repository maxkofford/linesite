<?php
namespace core\crud\crud_display;

class crud_html_accordian extends crud_display {
    const display_name = "crud_html_accordian";
    
    public function get_display_name() {
        return static::module_name;
    }
    public function echo_crud_multiple(\core\crud\crud_modules\crud_module $module, $module_input){
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
        <script>
        	var table_name="<?php echo $module->get_table_name() ?>";
        	$( document ).ready(function() {
        	/*
                $(".editable").change(function() {
					console.log("da name:" + $(this).attr("name"));
					console.log("da name:" + $(this).val());
					console.log("da name:" + table_name);
					var id_name = table_name + "_id";
					var id = "";
					var current = $(this).parent();
					
					var times = 0;
					while(id.length < 1 && times < 100){
						times++;
						var target = current.find("[name='"+id_name+"'");
						if(target.length > 0){
							id = target.html();
						} else {
							if(current.parent() != document){
								current = current.parent();
							} else {
								break;
							}
						}
					}
					
					console.log("da name:" + times);
					console.log("da name:" + id);
                });
                */
            });
        </script>
        <?php
        $data = $module->get_data_from_input($module_input);
        if(count($data) > 0){
        ?>
		<div class="data-row">
            <?php
            $typed_data = $module->get_column_types($data[0]);
            $typed_data = $module->column_name_post_process($typed_data);
            ?>
               
            <?php
            $data_row_spot = 0;
            foreach($data as $data_row){
                $data_row_spot++;
                static::echo_one_card($data_row_spot, $data_row, $module);
                
            }
            
            if(\core\Permissions::permission_level() == \core\Permissions::admin){
                $data_row_spot++;
                static::echo_one_card($data_row_spot, $data_row, $module, true);
            }
            
            
            ?>
			</div>
        <?php
            return true;
        } else {
            return false;
        }
    }
    
    
    private static function echo_one_card($data_row_spot, $data_row, $module, $do_blank = false){
        ?>
        <div class="card">
    		<div class="card-header" id="heading<?php echo $data_row_spot?>">
    			<h5 class="mb-0">
    				<button class="btn btn-link w-100" data-toggle="collapse"
    					data-target="#collapse<?php echo $data_row_spot?>" aria-expanded="<?php echo ($data_row_spot == 1) ? "true" : "false" ?>"
    					aria-controls="collapse<?php echo $data_row_spot?>">
                        <?php 
                        if($do_blank){
                            echo "New Row";
                        } else {
                            echo strlen($data_row[$module->get_row_title_name()]) > 0 ? $data_row[$module->get_row_title_name()] : $data_row[$module->get_table_name()."_id"];
                        }
                        ?>
    				</button>
    			</h5>
    		</div>

    		<div id="collapse<?php echo $data_row_spot?>" class="collapse <?php echo ($data_row_spot == 1) ? "show" : "" ?>"
    			aria-labelledby="heading<?php echo $data_row_spot?>" data-parent=".data-row">
    			<div class="card-body crud_row">
    			<?php
    			if(\core\Permissions::permission_level() == \core\Permissions::admin){
                    ?>
    				<form action="<?php echo \core\HTML::get_current_page()?>" method="post">
    				<input style="display:none;" name="action" value="update_single">
                	<?php 
    			}
    			if($do_blank){
        			foreach ($data_row as $name => $value){
                        $data_row[$name] = '';
        			}
    			}
                $html_pieces = $module->column_html($data_row);
                foreach($html_pieces as $name => $value){
                    if($value !== false && (!$do_blank || $name != $module->get_table_name()."_id")){
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
                if(\core\Permissions::permission_level() == \core\Permissions::admin){
                    ?>
                    <div class="row justify-content-md-center">
						<button class="col-2 btn" type="submit" value="Submit">Save</button>
					</div>
					 </form>
					 <form action="<?php echo \core\HTML::get_current_page()?>" method="post">
					 <input style="display:none;" name="action" value="delete">
					 <div class="row justify-content-md-center">
						<button class="col-2 btn" type="submit" value="Delete">Delete</button>
					</div>
					 </form>
                    <?php
                }
                ?>
                
        		</div>
    		</div>
        </div>
        <?php
    }
    
    public function update_data(\core\crud\crud_modules\crud_module $module, $input) {
        $module->bulk_update_row($input);
    }
}