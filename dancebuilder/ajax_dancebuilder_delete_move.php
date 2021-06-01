<?php
namespace dancebuilder;
define("skip_html", true);
require_once (__DIR__ . "/../apptop.php");
$data = \core\Input::GetAll();
if($data['dance_piece_id'] < 1){
    echo json_encode(['msg' => "invalid dance_piece_id"]);
    die();
}

\core\DB::run("delete from dance_piece where dance_piece_id = :dance_piece_id", $data);
echo json_encode(['msg' => "Dance move successfully deleted"]);
