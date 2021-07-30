<?php 

namespace core;

class Permissions {
    
    const admin = 1;
    
    
    
    public static function has_permission($permission_id, $user_id = null){
        if($user_id === null){
            
        }
        
        \core\DB::execute("SELECT * FROM user_to_permission WHERE user_id = :user_id", ['user_id' => $user_id]);
    }
    
    public static function permission_level(){
        return permission;
    }
}