<?php
require_once (__DIR__ . "/../apptop.php");
if(\core\Permissions::permission_level() != \core\Permissions::admin){
    \core\HTML::Redirect("/linesite/crud/crud_read_multiple.php");
}
?>
<?php require_once (__DIR__ . "/../appbottom.php"); ?>