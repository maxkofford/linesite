<?php
namespace core;

class HTML {
    public static function Redirect($url){
        ?>
        <head>
        <meta http-equiv="refresh" content="time; URL=<?= $url ?>" />
        </head>
        <?php
    }

    
    public static function Echo_Header(){
        if(!defined("skip_html")){
            ?>
            <HEAD>
                <link rel="stylesheet" href="\linesite\bootstrap.min.css">
                <script src="\linesite\jquery-3.6.0.min.js"></script> 
            </HEAD>
    		<?php   
        }
    }
}