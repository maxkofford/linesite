<?php
namespace Core;

class HTML {
    public static function Redirect($url){
        ?>
        <head>
        <meta http-equiv="refresh" content="time; URL=<?= $url ?>" />
        </head>
        <?php
    }
}