<?php
namespace dancebuilder;
define("skip_html", true);
require_once (__DIR__ . "/../apptop.php");
$data = \Core\Input::GetAll();
if($data['combined_move_id'] < 1 || $data['foot_move_id'] < 1 || $data['combined_move_to_foot_move_id'] < 1){
    echo json_encode(['msg' => "invalid combined_move_id or foot_move_id or combined_move_to_foot_move_id"]);
    die();
}
$rows_inserted = \Core\DB::BasicUpdate("combined_move_to_foot_move", $data);
echo json_encode(['msg' => "Combined move successfully updated!"]);
