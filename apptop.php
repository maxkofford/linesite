<?php
require_once (__DIR__ . "/vendor/autoload.php");
include_once (__DIR__ . "/configure.php");
define('HTTP_SERVER', 'https://' . $_SERVER['SERVER_NAME']);
define('HTTPS_SERVER', 'https://' . $_SERVER['SERVER_NAME']);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

\core\HTML::echo_header();
\core\HTML::echo_dropdown_menu();
?>

	