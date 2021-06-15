<?php
namespace core\crud\crud_modules;

class crud_module_dance_by_name extends \core\crud\crud_module {
    const table_name = "dance";
    const module_name = "dance_by_name";

    public function get_table_name() {
        return static::table_name;
    }
    public function get_module_name() {
        return static::module_name;
    }
    
    
    public function get_data_from_input($input) {
        $dance_song_name = "%" . $input . "%";
        return \core\DB::execute("SELECT * FROM " . static::table_name . " WHERE dance_song_name LIKE :dance_song_name", ["dance_song_name" => $dance_song_name]);
    }
    
    /**
     * 
SELECT concat('"', COLUMN_NAME , '" => "', COLUMN_NAME, '",')
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = 'linesite' AND TABLE_NAME = 'dance';
     * @return string[]
     */
    public function get_column_name_transform(){
        return ["dance_id" => "dance_id",
                "dance_song_name" => "dance_song_name",
                "dance_artist" => "dance_artist",
                "dance_song_youtube_id" => "dance_song_youtube_link",
                "dance_name" => "dance_name",
                "dance_counts" => "dance_counts",
                "dance_wall_count" => "dance_wall_count",
                "dance_starting_foot" => "dance_starting_foot",
                "dance_youtube_id" => "dance_instructions_youtube_link",
                "dance_move_sheet_link" => "dance_move_sheet_link",
                "dance_author_name" => "dance_author_name",
                "dance_is_special" => "dance_is_special",];
    }
    
    
    
    public function get_column_types($data) {
        $output_data = [];
        foreach($data as $name => $value){
            switch($name){
                case "dance_song_youtube_id":
                    $output_data[$name] = new \core\crud\crud_types\crud_type_youtube($name, $value);
                    break;
                case "dance_youtube_id":
                    $output_data[$name] = new \core\crud\crud_types\crud_type_youtube($name, $value);
                    break;
                case "dance_move_sheet_link":
                    $output_data[$name] = new \core\crud\crud_types\crud_type_link($name, $value);
                    break;
                case "dance_starting_foot":
                    $output_data[$name] = new \core\crud\crud_types\crud_type_foot($name, $value);
                    break;
                case "dance_is_special":
                    $output_data[$name] = new \core\crud\crud_types\crud_type_bool($name, $value);
                    break;
                default:
                    $output_data[$name] = new \core\crud\crud_types\crud_type_string($name, $value);
                    break;
            }
            
        }
        
        return $output_data;
    }
    

}