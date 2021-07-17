<?php
namespace core\crud\crud_display;

class crud_csv_generator extends crud_display {

    const display_name = "crud_csv_generator";

    public function get_display_name() {
        return static::module_name;
    }

    
    public function echo_crud_multiple(\core\crud\crud_modules\crud_module $module, $module_input){
        ?>
        <?php
        
        $data = $module->get_data_from_input($module_input);
        if(count($data) > 0){
            echo count($data) . " rows <br>";
            $output_file = 'max_data.csv';
            
            $fp = fopen($output_file, 'w');
            
            $typed_data = $module->column_string($data[0]);
            $cleaned_data = [];
            foreach($typed_data as $name => $value){
                if($value !== false){
                    $cleaned_data[] = $name;
                }
            }
            fputcsv($fp, $cleaned_data);
            
            foreach($data as $data_row){
                $typed_data = $module->column_string($data_row);
                
                $cleaned_data = [];
                foreach($typed_data as $name => $value){
                    if($value !== false){
                        $cleaned_data[] = $value;
                    }
                }
                fputcsv($fp, $cleaned_data);
            }

            fclose($fp);
            
            
        ?>
		<a href="<?php echo $output_file?>" download>
      		<?php echo $output_file?>
        </a>
        <?php
            return true;
        } else {
            return false;
        }
    }
}