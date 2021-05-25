<?php
namespace dancebuilder;
define("skip_html", true);
require_once (__DIR__ . "/../apptop.php");
$data = \Core\Input::GetAll();
if($data['dance_id'] < 1 || $data['foot_move_id'] < 1){
    echo json_encode(['msg' => "invalid dance_id or move_id"]);
    die();
}

\Core\DB::execute("select * from combined_move_to_foot_move where combined_move_id = :combined_move_id");

$new_id = \Core\DB::BasicInsert("dance_piece", $data);
echo json_encode(['msg' => "Dance move successfully added to combined move!", 'dance_piece_id' => $new_id]);
