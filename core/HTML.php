<?php
namespace core;

class HTML {
    public static function Redirect($url){
        ?>
        <head>
        <meta http-equiv="refresh" content="time; URL=<?= $url ?>" />
        </head>
        <?php
        die();
    }

    
    public static function echo_header(){
        if(!defined("skip_html")){
            ?>
            <HEAD>
                <link rel="stylesheet" href="\linesite\bootstrap.min.css">
                <script src="\linesite\jquery-3.6.0.min.js"></script> 
                <script src="\linesite\bootstrap.bundle.js"></script> 
            </HEAD>
            <body>
    		<?php   
        }
    }
    
    public static function echo_dropdown_menu(){
        if(!defined("skip_html")){
            ?>
            <div class="dropdown container-fluid p-2">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Menu
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                	<?php static::echo_search_box(); ?>
                </div>
            </div>
            <?php
        }
    }
    
    public static function echo_search_box(){
        ?>
        <form class="col" action="/linesite/crud/crud_read_multiple.php" method="post">
            <input type="hidden" name="module" value="dance_by_name">
            <div class="input-group">
                <input type="search" class="form-control" placeholder="Search for dance here!" name="module_input" value="">
                <div class="input-group-append">
                	<button class="btn btn-secondary form-control" type="submit">Search</button>
                </div>
            </div>
        </form>
        <?php
    }
}