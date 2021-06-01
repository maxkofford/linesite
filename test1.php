<?php
require_once(__DIR__ . "/apptop.php");
$results = \core\DB::execute("select * from test");
foreach($results as $yeet){
    echo $yeet['other'];
}
echo "yay";