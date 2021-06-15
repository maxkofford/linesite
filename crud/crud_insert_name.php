<?php
require_once (__DIR__ . "/../apptop.php");
if(\core\Permissions::permission_level() != \core\Permissions::admin){
    \core\HTML::Redirect("/linesite/crud/crud_read_multiple.php");
}
?>
<form action="/linesite/crud/crud_insert_input.php">
	<div><span>target table:</span> <input type="text" name="table_name"></div>
	<div><input type="submit" value="Submit"></div>
</form>

<?php require_once (__DIR__ . "/../appbottom.php"); ?>