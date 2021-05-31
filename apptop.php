<?php
require_once (__DIR__ . "/vendor/autoload.php");
include_once (__DIR__ . "/configure.php");
define('HTTP_SERVER', 'https://' . $_SERVER['SERVER_NAME']);
define('HTTPS_SERVER', 'https://' . $_SERVER['SERVER_NAME']);



\Core\HTML::Echo_Header();