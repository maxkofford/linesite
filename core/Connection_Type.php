<?php

namespace core;

class Connection_Type {
    const WRITE = 0; // Master
    const READ = 1;
    const REPORTING = 2;

    public static function get_connection_details($connection_type = self::WRITE) {
        switch ($connection_type) {
            case self::WRITE:
                $server = DB_SERVER;
                $database = DB_DATABASE;
                $username = DB_SERVER_USERNAME;
                $password = DB_SERVER_PASSWORD;
                break;
            case self::READ:
                $server = DB_SERVER2;
                $database = DB_DATABASE2;
                $username = DB_SERVER_USERNAME2;
                $password = DB_SERVER_PASSWORD2;
                break;
            case self::REPORTING:
                $server = DB_REPORT_SERVER;
                $database = DB_REPORT_DATABASE;
                $username = DB_REPORT_USERNAME;
                $password = DB_REPORT_PASSWORD;
                break;
            default:
                throw new \Exception("{$connection_type} is not a valid connection type");
                break;
        }

        return [
            'dsn' => "mysql:host=" . $server . ";dbname=" . $database,
            'user' => $username,
            'password' => $password
        ];
    }
}