<?php
namespace core\crud\crud_display;

class crud_html_table extends crud_display {
    const display_name = "crud_html_table";
    
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
            });	
        </script>
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
                	<th><?php echo "Save Changes"?></th>
                </tr>
            </thead>
			<tbody>
            <?php
            foreach($data as $data_row){
                ?>
                <tr class="crud_row">
                <?php
    			if(\core\Permissions::permission_level() == \core\Permissions::admin){
                    ?>
    				<form action="<?php echo \core\HTML::get_current_page()?>" method="post">
                	<?php 
    			}
                $html_pieces = $module->column_html($data_row);
                foreach($html_pieces as $value){
                    if($value !== false){
                    ?>
                    <td>
                    <?php
                    echo trim($value);
                    ?>
                    </td>
                    <?php  
                    }
                }
                if(\core\Permissions::permission_level() == \core\Permissions::admin){
                    ?>
                        <td>
                        	<input style="display:none;" name="action" value="update_single">
                            <div class="row justify-content-md-center">
        						<button class="col-2 btn" type="submit" value="Submit">Save</button>
        					</div>
    					</td>
					 </form>
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
            return true;
        } else {
            return false;
        }
    }
    
    public function update_data(\core\crud\crud_modules\crud_module $module, $input) {
        $module->bulk_update_row($input);
    }
    
    public function delete(\core\crud\crud_modules\crud_module $module, $input) {
        $module->bulk_update_row($input);
    }
}