<?php
namespace dancebuilder;
require_once (__DIR__ . "/../apptop.php");
$dance_id = \Core\Input::Get('dance_id', '');
if(strlen($dance_id) < 1){
    ?>
    <div class="column message bg-primary text-light p-2 rounded border d-none">
    	Missing a selected dance!
	</div>
    <?php
    die();
} 
$moves = \Core\DB::execute("select * from foot_move");
$dance = \Core\DB::execute("select * from dance where dance_id = :dance_id",["dance_id" => $dance_id]);
$dance = $dance[0];
$dance_pieces = \Core\DB::execute("select * from dance_piece inner join foot_move using (foot_move_id) where dance_id = :dance_id order by dance_piece_position asc", ["dance_id" => $dance_id]);
$directions = ["","↙", "↓", "↘", "←", "-", "→", "↖", "↑", "↗"];
//$rotate_right = "↻ ";
//$rotate_left = "↺ ";
$dance_cookie = \Core\Input::GetCookie("dancebuilder", '{"dance_piece_facing_direction":"5","dance_piece_moving_direction":"5"}');
$dance_cookie = json_decode($dance_cookie);
$initial_facing_direction = $dance_cookie->dance_piece_facing_direction;
$initial_moving_direction = $dance_cookie->dance_piece_moving_direction;
?>
<script>
var dance_id = "<?php echo $dance_id ?>";
var dance_piece_facing_direction = <?php echo $initial_facing_direction ?>;
var dance_piece_moving_direction = <?php echo $initial_moving_direction ?>;

$(function() {
	$(".moving_buttons button[data-num='<?php echo $initial_moving_direction ?>']").removeClass("btn-default");
	$(".moving_buttons button[data-num='<?php echo $initial_moving_direction ?>']").addClass("btn-dark");
	$(".facing_buttons button[data-num='<?php echo $initial_facing_direction ?>']").removeClass("btn-default");
	$(".facing_buttons button[data-num='<?php echo $initial_facing_direction ?>']").addClass("btn-dark");
    $(".moving_buttons button").click(function() {
    	var old_button = $(this).parent().parent().parent().find("button[data-num='"+dance_piece_moving_direction+"'");
		old_button.removeClass("btn-dark");
		old_button.addClass("btn-default");
    	dance_piece_moving_direction = $(this).attr("data-num");
    	$(this).removeClass("btn-default");
    	$(this).addClass("btn-dark");
    });
	$(".facing_buttons button").click(function() {
		var old_button = $(this).parent().parent().parent().find("button[data-num='"+dance_piece_facing_direction+"'");
		old_button.removeClass("btn-dark");
		old_button.addClass("btn-default");
    	dance_piece_facing_direction = $(this).attr("data-num");
    	$(this).removeClass("btn-default");
    	$(this).addClass("btn-dark");
    });
    $(".add_move").click(function() {
    	var foot_move_id = $(".foot_move_id").val();
    	var dance_piece_length = $(".dance_piece_length").val();
    	$.ajax({
          method: "POST",
          url: "/linesite/dancebuilder/dancebuilder_add_move.php",
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
              	return;
          	}
          	var cookie_data = {
          	dance_piece_facing_direction: dance_piece_facing_direction, 
          	dance_piece_moving_direction: dance_piece_moving_direction};
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
        	<div class="col message bg-primary text-light p-2 rounded border d-none">
        	</div>
    	</div>
    	<div class="row">
        	<div class="col">
        		<div class="row w-100 dance_pieces">
					<?php foreach ( $dance_pieces as $current_piece){ ?>
					<div class="col <?php echo $current_piece['dance_piece_length'] == 1 ? "col8th" : "col16th" ?> rounded border" data-id="<?php echo $current_piece['dance_piece_id']?>">
						<?php echo $current_piece['foot_move_name'] . " F".$directions[$current_piece['dance_piece_facing_direction']] . " M".$directions[$current_piece['dance_piece_moving_direction']]?>
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
        		<button class="btn btn-default add_move">Add Move</button>
        	</div>
        </div>
	</div>
</div>