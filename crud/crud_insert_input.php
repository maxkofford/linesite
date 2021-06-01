<?php
require_once (__DIR__ . "/../apptop.php");

$table_name = \core\Input::Get('table_name', '');


if (strlen($table_name) > 0) {
    $results = \core\DB::execute("
    SELECT COLUMN_NAME, IS_NULLABLE, DATA_TYPE
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = :db_name AND TABLE_NAME = :table_name", [
        'db_name' => DB_name,
        'table_name' => $table_name
    ]);
    echo 'inserting for ' . $table_name . '<br>';
    ?>
	<form action="/linesite/crud/crud_insert_results.php">
		<input type="hidden" name="table_name" value="<?= $table_name ?>">
    <?php
    foreach ($results as $entry) {
        if($entry['COLUMN_NAME'] != $table_name . "_id") {
        ?>   
        <div>
			<span><?= $entry['COLUMN_NAME'] . "(" . $entry['DATA_TYPE'] . ")" . (( $entry['IS_NULLABLE'] == "NO") ? '(required)' : '') ?>:</span> 
			<input type="text" name="<?= $entry['COLUMN_NAME'] ?>">
		</div>
		<?php
		}
    }
    ?>
    	<div><input type="submit" value="Submit"></div>
    </form>
<?php
}
else {
    \core\HTML::Redirect("/linesite/crud/crud_insert_name.php");
}
?>