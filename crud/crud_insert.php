<?php
require_once (__DIR__ . "/../apptop.php");

$table_name = \Core\Input::Get('table_name', '');


if (strlen($table_name) > 0) {
    $results = \Core\DB::execute("
    SELECT COLUMN_NAME
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = :db_name AND TABLE_NAME = :table_name", [
        'db_name' => DB_name,
        'table_name' => $table_name
    ]);
    echo $table_name . '<br>';
    foreach ($results as $entry) {
        echo $entry['COLUMN_NAME'] . '<br>';
    }
}
else {
    \Core\HTML::Redirect("/linesite/crud/crud_insert_name.php");
}
?>