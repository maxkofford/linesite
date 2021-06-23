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
            	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <link rel="stylesheet" href="\linesite\bootstrap.min.css">
                <script src="\linesite\jquery-3.6.0.min.js"></script> 
                <script src="\linesite\bootstrap.bundle.js"></script>
            </HEAD>
            <body>
            	<div class="container" style="min-height: 200px;">
    		<?php   
        }
    }
    
    public static function echo_dropdown_menu(){
        
        
        /*
         
            //animated dropdown
            <style>
.dropdown-menu {
    display: block;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.5s;
    margin:0;
    padding:0;
    border: none;
}

.dropdown-menu.show {
    border: 1px solid rgba(0,0,0,.15);
    padding: .5rem 0;
    margin: .125rem 0 0;
    max-height: 2000px;
}
            </style> 
         
         */
        
        
        if(!defined("skip_html")){
            ?>
            
            <div class="dropdown p-2">
                <button class="btn btn-secondary dropdown-toggle btn-lg" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Menu
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                	<?php static::echo_search_box(); ?>
                	<?php if(\core\Permissions::permission_level() == \core\Permissions::admin){ ?>
                	<a class="row col py-2 no-gutters" href="/linesite/whit/sedar_search.php">Sedar Search</a>
                	<?php } ?>
                </div>
            </div>
            <?php
        }
    }
    
    public static function echo_search_box(){
        ?>
        <form class="row col no-gutters" action="/linesite/crud/crud_read_multiple.php" method="post">
            <input type="hidden" name="module" value="dance_by_name">
            <div class="input-group">
                <input type="search" class="form-control" placeholder="Search for song here!" name="module_input" value="<?php echo \core\Input::Get("module_input", "")?>">
                <div class="input-group-append">
                	<button class="btn btn-secondary form-control" type="submit">Search</button>
                </div>
            </div>
        </form>
        <?php
    }
}