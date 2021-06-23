<?php

namespace core;

/**
 * Class HTTP_Request_Helper - Adds some helper methods for doing post requests
 *
 * @package core\Utilities
 */
class HTTP_Request_Helper {
    
    /**
     * Does a post request using curl
     *
     * @param string $url
     *            the url to send to
     * @param array $post_fields_array
     *            the data to be sent to the post request after being json encoded
     * @param array $header
     *            allows you to override the header if you want more stuff
     * @param array $extra_curl_opts
     *            curl opt codes to values you want set
     * @return mixed
     */
    public static function post_request($url, $post_fields_array, $header = ['Content-Type: application/json'], $extra_curl_opts = [], $json_decode = true) {
        $fields_string = json_encode($post_fields_array);
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSLVERSION, 1);
        
        foreach ($extra_curl_opts as $opt_code => $opt_value) {
            curl_setopt($ch, $opt_code, $opt_value);
        }
        
        $result = curl_exec($ch);
        if($json_decode){
            $result = json_decode($result);
        }
        curl_close($ch);
        
        return $result;
    }
}