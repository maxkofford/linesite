<?php
namespace core\crud\crud_modules;

class crud_module_dance_by_name extends \core\crud\crud_modules\crud_module {
    const table_name = "dance";
    const module_name = "dance_by_name";
    const row_title_name = "dance_song_name";

    public function get_table_name() {
        return static::table_name;
    }
    public function get_module_name() {
        return static::module_name;
    }
    public function get_row_title_name() {
        return static::row_title_name;
    }
    
    
    public function get_data_from_input($input) {
        if($input == "all"){
            return \core\DB::execute("
SELECT * FROM dance WHERE dance_is_special = 0 ORDER BY dance_song_name ASC");
        }
        
        \core\DB::run("INSERT INTO search_log (search_log_text, search_log_date) VALUES (:search_log_text, now());", ['search_log_text' => $input]);
        
        $dance_song_name = "%" . $input . "%";
        return \core\DB::execute("
SELECT * FROM dance WHERE 
(dance_song_name LIKE :dance_song_name 
OR dance_artist LIKE :dance_song_name
OR dance_name LIKE :dance_song_name) 
AND dance_is_special = 0
ORDER BY dance_song_name ASC", ["dance_song_name" => $dance_song_name]);
    }
    
    /**
     * 
SELECT concat('"', COLUMN_NAME , '" => "', COLUMN_NAME, '",')
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = 'linesite' AND TABLE_NAME = 'dance';
     * @return string[]
     */
    public function get_column_name_transform(){
        if(\core\Permissions::permission_level() == \core\Permissions::admin){
            return ["dance_id" => "dance_id",
                    "dance_song_name" => "dance_song_name",
                    "dance_artist" => "dance_artist",
                    "dance_song_youtube_id" => "dance_song_youtube_id",
                    "dance_name" => "dance_name",
                    "dance_counts" => "dance_counts",
                    "dance_wall_count" => "dance_wall_count",
                    "dance_starting_foot" => "dance_starting_foot",
                    "dance_youtube_id" => "dance_instructions_youtube_id",
                    "dance_move_sheet_link" => "dance_move_sheet_link",
                    "dance_author_name" => "dance_author_name",
                    "dance_is_special" => "dance_is_special",
                    "dance_bpm" => "dance_bpm",
                    "dance_duration" => "dance_duration",
                    "user_id" => "user_id",
            ];
        } else {
            return ["dance_id" => "dance_id",
                    "dance_song_name" => "Song Name",
                    "dance_artist" => "Song Performer",
                    "dance_song_youtube_id" => "Song Youtube",
                    "dance_name" => "Dance Name",
                    "dance_counts" => "Dance Counts",
                    "dance_wall_count" => "Wall Counts",
                    "dance_starting_foot" => "Starting Weight Foot",
                    "dance_youtube_id" => "Instructions Youtube",
                    "dance_move_sheet_link" => "Move Sheet Link",
                    "dance_author_name" => "Dance Choreographer",
                    "dance_is_special" => "dance_is_special",
                    "dance_bpm" => "dance_bpm",
                    "dance_duration" => "dance_duration",
                    "user_id" => "user_id",
            ];
        }
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
                case "dance_wall_count":
                    $output_data[$name] = new \core\crud\crud_types\crud_type_walls($name, $value);
                    break;
                case "dance_is_special":
                    if(\core\Permissions::permission_level() == \core\Permissions::admin){
                        $output_data[$name] = new \core\crud\crud_types\crud_type_bool($name, $value);
                    } else {
                        $output_data[$name] = new \core\crud\crud_types\crud_type_hide($name, $value);
                    }
                    break;
                case "dance_id":
                    $output_data[$name] = new \core\crud\crud_types\crud_type_main_id($name, $value);
                    break;
                case "dance_bpm":
                case "dance_duration":
                case "user_id":
                    if(\core\Permissions::permission_level() == \core\Permissions::admin){
                        $output_data[$name] = new \core\crud\crud_types\crud_type_string($name, $value);
                    } else {
                        $output_data[$name] = new \core\crud\crud_types\crud_type_hide($name, $value);
                    }
                    break;
                default:
                    $output_data[$name] = new \core\crud\crud_types\crud_type_string($name,$value);
                    break;
            }
            
        }
        
        return $output_data;
    }


    

}