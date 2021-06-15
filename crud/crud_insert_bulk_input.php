<?php
require_once (__DIR__ . "/../apptop.php");
?>
<form action="upload.php" method="post" enctype="multipart/form-data">
	<div><span>target table:</span><input type="text" name="table_name"></div>
	<div><span>Copy/Paste from excel with headers</span></div>
	<div><textarea name="excel_data" style="width:250px;height:150px;"></textarea></div>
	<div><span>CSV File</span></div>
	<input type="file" name="fileToUpload" id="fileToUpload">
	<div><input type="submit" value="Submit"></div>
</form>
<?php require_once (__DIR__ . "/../appbottom.php"); ?>