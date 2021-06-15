<?php
require_once (__DIR__ . "/../apptop.php");

$input = \core\Input::GetAll();

if (array_key_exists('table_name', $input) && strlen($input['table_name']) > 0) {
    $table_name = $input['table_name'];
    unset($input['table_name']);
    $new_id = \core\DB::BasicInsert($table_name, $input);
    if($new_id > 0){
        ?>
        Successfully added a entry to <?= $table_name ?> <br>
        <div><a href="/linesite/crud/crud_insert_input.php?table_name=<?=$table_name?>">Add Another</a></div>
        <?php 
    } else {
        ?>
        Problem inserting data.
        <?php
    }
}
else {
    
    \core\HTML::Redirect("/linesite/crud/crud_insert_name.php");
}
?>
<?php require_once (__DIR__ . "/../appbottom.php"); ?>