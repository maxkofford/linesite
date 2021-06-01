<?php
namespace dancebuilder;
require_once (__DIR__ . "/../apptop.php");
$dance_id = \core\Input::Get('dance_id', '');
$combined_move_id = \core\Input::Get('combined_move_id', '');
if(strlen($dance_id) < 1 && strlen($combined_move_id) < 1){
    ?>
    <div class="column message bg-primary text-light p-2 rounded border d-none">
    	Missing a selected dance!
	</div>
    <?php
    die();
}

$moves = \core\DB::execute("select * from foot_move");

$combined_move = [];
$combined_pieces = [];
if(strlen($combined_move_id) > 0){
    $combined_move = \core\DB::execute("select * from combined_move where combined_move_id = :combined_move_id", ['combined_move_id' => $combined_move_id]);
    $combined_move = $combined_move[0];
    $combined_pieces = \core\DB::execute("select * from combined_move_to_foot_move inner join foot_move using (foot_move_id) where combined_move_id = :combined_move_id order by combined_move_to_foot_move_id asc", ['combined_move_id' => $combined_move_id]);
}

$dance = [];
$dance_pieces = [];
if(strlen($dance_id) > 0){
    $dance = \core\DB::execute("select * from dance where dance_id = :dance_id",["dance_id" => $dance_id]);
    $dance = $dance[0];
    $dance_pieces = \core\DB::execute("select * from dance_piece inner join foot_move using (foot_move_id) where dance_id = :dance_id order by dance_piece_id asc", ["dance_id" => $dance_id]);
}

//¼ ½ ¾ 
$directions = ["","↙", "↓", "↘", "←", "-", "→", "↖", "↑", "↗", "S"];
//$rotate_right = "↻ ";
//$rotate_left = "↺ ";
$dance_cookie = \core\Input::GetCookie("dancebuilder", '{"dance_piece_facing_direction":"5","dance_piece_moving_direction":"5","msg":""}');
$dance_cookie = json_decode($dance_cookie);
$initial_facing_direction = $dance_cookie->dance_piece_facing_direction;
$initial_moving_direction = $dance_cookie->dance_piece_moving_direction;
?>
<script>
var dance_id = "<?php echo $dance_id ?>";
var combined_move_id = "<?php echo $combined_move_id ?>";
var dance_piece_facing_direction = <?php echo $initial_facing_direction ?>;
var dance_piece_moving_direction = <?php echo $initial_moving_direction ?>;
var update_piece_id = -1;

$(function() {
	$(".moving_buttons button[data-num='<?php echo $initial_moving_direction ?>']").removeClass("btn-default");
	$(".moving_buttons button[data-num='<?php echo $initial_moving_direction ?>']").addClass("btn-dark");
	$(".facing_buttons button[data-num='<?php echo $initial_facing_direction ?>']").removeClass("btn-default");
	$(".facing_buttons button[data-num='<?php echo $initial_facing_direction ?>']").addClass("btn-dark");
	
    $(".moving_buttons button").click(function() {
    	var old_button = $(".moving_buttons").find("button[data-num='"+dance_piece_moving_direction+"'");
		var new_button = $(this);
		direction_visible_button_change(old_button, new_button);
    	dance_piece_moving_direction = new_button.attr("data-num");
    });
    
	$(".facing_buttons button").click(function() {
		var old_button = $(".facing_buttons").find("button[data-num='"+dance_piece_facing_direction+"'");
		var new_button = $(this);
		direction_visible_button_change(old_button, new_button); 
    	dance_piece_facing_direction = new_button.attr("data-num");
    });
    
    function direction_visible_button_change(old_button, target_button) {
    	old_button.removeClass("btn-dark");
		old_button.addClass("btn-default");
    	target_button.removeClass("btn-default");
    	target_button.addClass("btn-dark");
    }
    
    $(".dance_piece_button").click(function() {
    	$(".dance_piece_button").removeClass("border-danger");
    	$(this).addClass("border-danger");
    	update_piece_id = $(this).attr("data-id");
    });
    
	$(".delete_button").click(function() {
    	var dance_piece_id = $(this).parent().attr("data-id");
    	$.ajax({
              method: "POST",
              url: "/linesite/dancebuilder/ajax_dancebuilder_delete_move.php",
              data: {
              	dance_piece_id: dance_piece_id
              }
            })
              .done(function( data ) {
              	try{
              		data = JSON.parse(data);
              	} catch (e) {
                  	$(".message").html(data);
                  	$(".message").removeClass("d-none");
                  	return;
              	}
              	var cookie_data = {
              	dance_piece_facing_direction: dance_piece_facing_direction, 
              	dance_piece_moving_direction: dance_piece_moving_direction,
              	msg: data.msg};
              	document.cookie = "dancebuilder=" + JSON.stringify(cookie_data);
              	location.reload(); 
              });
    });

    $(".update_move_button").click(function() {
    	if(update_piece_id != -1){
    		var foot_move_id = $(".foot_move_id").val();
    		var dance_piece_length = $(".dance_piece_length").val();
        	$.ajax({
              method: "POST",
              url: "/linesite/dancebuilder/ajax_dancebuilder_update_move.php",
              data: {
              	dance_id: dance_id,
              	dance_piece_id: update_piece_id,
              	foot_move_id: foot_move_id, 
              	dance_piece_facing_direction: dance_piece_facing_direction,
    			dance_piece_moving_direction: dance_piece_moving_direction,
    			dance_piece_length: dance_piece_length
              }
            })
              .done(function( data ) {
              	try{
              		data = JSON.parse(data);
              	} catch (e) {
                  	$(".message").html(data);
                  	$(".message").removeClass("d-none");
                  	return;
              	}
              	var cookie_data = {
              	dance_piece_facing_direction: dance_piece_facing_direction, 
              	dance_piece_moving_direction: dance_piece_moving_direction,
              	msg: data.msg};
              	document.cookie = "dancebuilder=" + JSON.stringify(cookie_data);
              	location.reload(); 
              });
    	}
    });
    $(".add_move_button").click(function() {
    	var foot_move_id = $(".foot_move_id").val();
    	var dance_piece_length = $(".dance_piece_length").val();
    	$.ajax({
          method: "POST",
          url: "/linesite/dancebuilder/ajax_dancebuilder_insert_move.php",
          data: {
          	dance_id: dance_id,
          	foot_move_id: foot_move_id, 
          	dance_piece_facing_direction: dance_piece_facing_direction,
			dance_piece_moving_direction: dance_piece_moving_direction,
			dance_piece_length: dance_piece_length
          }
        })
          .done(function( data ) {
          	try{
          		data = JSON.parse(data);
          	} catch (e) {
              	$(".message").html(data);
              	$(".message").removeClass("d-none");
              	return;
          	}
          	var cookie_data = {
          	dance_piece_facing_direction: dance_piece_facing_direction, 
          	dance_piece_moving_direction: dance_piece_moving_direction,
          	msg: data.msg};
          	document.cookie = "dancebuilder=" + JSON.stringify(cookie_data);
          	location.reload(); 
          });
    });
    
});
</script>
<style>
.col8th{
    flex: 0 0 12.5%;
    max-width: 12.5%;
}
.col16th{
    flex: 0 0 6.25%;
    max-width: 6.25%;
}
.dance_piece_template{
    display: none;
}
.dance_pieces{
    flex-wrap: wrap;
}
</style>

<div class="col rounded border dance_piece_template" data-id="">
</div>

<div class="mx-5 my-4">
	<div class="container-fluid">
		<div class="row">
        	<div class="col">
        		<?php 
        		if(strlen($dance_id) > 0){ 
        		    echo "Dance -" . $dance['dance_name'];
        		} 
        		if(strlen($combined_move_id) > 0){
        		    echo "Combined Move - " . $combined_move["combined_move_name"] . " - " . $combined_move["combined_move_description"];
        		}
        		?>
        	</div>
    	</div>
    	<div class="row">
        	<div class="col message bg-primary text-light p-2 rounded border <?php echo strlen($dance_cookie->msg) > 1 ? "" : "d-none" ?>">
        		<?php echo $dance_cookie->msg ?>
        	</div>
    	</div>
    	<div class="row">
        	<div class="col">
        		<div class="row w-100 dance_pieces">
					<?php foreach ($dance_pieces as $current_piece){ ?>
					<div class="col <?php echo $current_piece['dance_piece_length'] == 1 ? "col8th" : "col16th" ?> rounded border dance_piece_button" data-id="<?php echo $current_piece['dance_piece_id']?>">
						<?php echo $current_piece['foot_move_name'] . " F".$directions[$current_piece['dance_piece_facing_direction']] . " M".$directions[$current_piece['dance_piece_moving_direction']]?>
						<span class="text-danger delete_button">X</span>
					</div>
					<?php } ?>
				</div>
				<div class="row w-100 dance_pieces">
					<?php foreach ($combined_pieces as $current_piece){ ?>
					<div class="col col16th rounded border dance_piece_button" data-id="<?php echo $current_piece['combined_move_to_foot_move_id']?>">
						<?php echo $current_piece['foot_move_name'] . " M". $directions[$current_piece['combined_move_to_foot_move_direction']]?>
						<button class="btn btn-default text-danger">X</button>
					</div>
					<?php } ?>
				</div>
        	</div>
    	</div>
		<div class="row move_data">
			<div class="col-4">
				Dance Moves 
				<div>
				<select class="foot_move_id" name="foot_move_id">
					<option value="-1">Select a Move</option>
            		<?php foreach ($moves as $move) { ?>
            		    <option value="<?php echo $move['foot_move_id']?>"><?php echo $move['foot_move_name']?></option>
            		<?php } ?>
        		</select>
        		</div>
        		<div>
        			<select class="dance_piece_length" name="dance_piece_length">
    					<option value="1">Regular</option>
                		<option value="2">Half</option>
        			</select>
        		</div>
        		
			</div>
			<div class="col-4 moving_buttons">
				Moving Direction
				<div class="row">
					<div class="col-3">
    				</div>
					<div class="col-3">
						<button class="btn btn-default w-100 m-2" data-num="7">Forward-Left</button>
					</div>
					<div class="col-3">
						<button class="btn btn-default w-100 m-2" data-num="8">Forward</button>
					</div>
					<div class="col-3">
						<button class="btn btn-default w-100 m-2" data-num="9">Forward-Right</button>
					</div>
				</div>
				<div class="row">
					<div class="col-3">
						<button class="btn btn-default w-100 m-2" data-num="10">Side</button>
					</div>
					<div class="col-3">
						<button class="btn btn-default w-100 m-2" data-num="4">Left</button>
					</div>
					<div class="col-3">
						<button class="btn btn-default w-100 m-2" data-num="5">-</button>
					</div>
					<div class="col-3">
						<button class="btn btn-default w-100 m-2" data-num="6">Right</button>
					</div>
				</div>
				<div class="row">
    				<div class="col-3">
    				</div>
					<div class="col-3">
						<button class="btn btn-default w-100 m-2" data-num="1">Back-Left</button>
					</div>
					<div class="col-3">
						<button class="btn btn-default w-100 m-2" data-num="2">Back</button>
					</div>
					<div class="col-3">
						<button class="btn btn-default w-100 m-2" data-num="3">Back-Right</button>
					</div>
				</div>
			</div>
			<div class="col-4 facing_buttons">
				Facing Direction
				<div class="row">
					<div class="col-4">
						<button class="btn btn-default w-100 m-2" data-num="7">Forward-Left</button>
					</div>
					<div class="col-4">
						<button class="btn btn-default w-100 m-2" data-num="8">Forward</button>
					</div>
					<div class="col-4">
						<button class="btn btn-default w-100 m-2" data-num="9">Forward-Right</button>
					</div>
				</div>
				<div class="row">
					<div class="col-4">
						<button class="btn btn-default w-100 m-2" data-num="4">Left</button>
					</div>
					<div class="col-4">
						<button class="btn btn-default w-100 m-2" data-num="5">-</button>
					</div>
					<div class="col-4">
						<button class="btn btn-default w-100 m-2" data-num="6">Right</button>
					</div>
				</div>
				<div class="row">
					<div class="col-4">
						<button class="btn btn-default w-100 m-2" data-num="1">Back-Left</button>
					</div>
					<div class="col-4">
						<button class="btn btn-default w-100 m-2" data-num="2">Back</button>
					</div>
					<div class="col-4">
						<button class="btn btn-default w-100 m-2" data-num="3">Back-Right</button>
					</div>
				</div>
			</div>
		</div>
        <div class="row move_data">
        	<div class="col">
        		<button class="btn btn-default add_move_button">Add Move</button>
        	</div>
        	<div class="col">
        		<button class="btn btn-default update_move_button">Update Move</button>
        	</div>
        </div>
	</div>
</div>