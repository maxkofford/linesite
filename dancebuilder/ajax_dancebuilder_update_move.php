<?php
namespace dancebuilder;
define("skip_html", true);
require_once (__DIR__ . "/../apptop.php");
$data = \Core\Input::GetAll();
if($data['dance_id'] < 1 || $data['foot_move_id'] < 1 || $data['dance_piece_id'] < 1){
    echo json_encode(['msg' => "invalid dance_id, move_id, or dance_piece_id"]);
    die();
}
\Core\DB::BasicUpdate("dance_piece", $data);
echo json_encode(['msg' => "Dance move in song successfully updated!"]);
