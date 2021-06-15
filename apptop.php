<?php
require_once (__DIR__ . "/vendor/autoload.php");
include_once (__DIR__ . "/configure.php");
define('HTTP_SERVER', 'https://' . $_SERVER['SERVER_NAME']);
define('HTTPS_SERVER', 'https://' . $_SERVER['SERVER_NAME']);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

\core\HTML::Echo_Header();
?>
<body>
	<div class="dropdown">
  		<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Dropdown
    </button>
	<div class="dropdown-menu" aria-labelledby="dropdownMenu2">
    	<button class="dropdown-item" type="button">Action</button>
    	<button class="dropdown-item" type="button">Another action</button>
    	<button class="dropdown-item" type="button">Something else here</button>
  	</div>
</div>