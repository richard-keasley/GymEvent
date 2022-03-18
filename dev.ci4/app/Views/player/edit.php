<?php $this->extend('default'); 
$player = $event->player;
if(!$player) $player = [\App\Entities\Event::player_row];

$this->section('content');
#d($event);
#d($player);
#$track = new \App\Libraries\Track;
#$track->event_id = $event->id;
#d($event_tracks);

$player_tracks = []; // all tracks listed on player
echo form_open(base_url(uri_string()));
?>
<input type="hidden" name="view" value="admin">
<div id="playervar">
<?php foreach($player as $round) { 
$player_tracks[$round['exe']] = $round['entry_nums'];
?>
<div class="datarow row row-fluid py-2 border-bottom">
<div class="col" style="max-width:25em;">
	<div class="row" >
		<div class="col-9">
			<input class="form-control" data-name="title" placeholder="title" value="<?php echo $round['title'];?>">
		</div>
		<div class="col-3">
			<input class="form-control" data-name="exe" placeholder="exercise" value="<?php echo $round['exe'];?>">
		</div>
	</div>
	<div class="mt-3">
		<input class="form-control" data-name="description" placeholder="description" value="<?php echo $round['description'];?>">
	</div>
</div>
<div class="col">
	<textarea class="form-control" rows="3" columns="20" data-name="entry_nums" placeholder="Exercise entry numbers"><?php echo implode(' ', $round['entry_nums']);?></textarea>
</div>
<div class="col-auto">
	<div class="btn-group-vertical">
	<button type="button" name="up" class="btn bi-arrow-up-circle btn-info"></button>
	<button type="button" name="del" class="btn bi-trash btn-danger" title="delete"></button>
	</div>
</div>
</div>
<?php } ?>
<button name="add" type="button" class="btn bi-plus-square btn-success" title="add round"></button>
<input type="hidden" name="player" value="">
<script>
const fields = ['exe','title','description','entry_nums'];
$(function(){
$('[name=update]').click(function() {
	var player = []; 
	$('#playervar .datarow').each(function() {
		var datarow = this, player_row = {};
		fields.forEach(function(item, index) {
			player_row[item] = $(datarow).find('[data-name='+item+']').val().trim();
		});
		player_row['entry_nums'] = player_row['entry_nums'].split(/[^\d]+/);
		player.push(player_row);
	});
	$('[name=player]').val(JSON.stringify(player));
	$(this).closest('form').submit();
});

$('#playervar [name=add]').click(function() {
	var $row = $('#playervar .datarow:last');
	var $clone = $row.clone(true);
	$clone.find('input').val('');
	$clone.find('textarea').val('');
	$row.after($clone);
});

$('#playervar [name=del]').click(function() {
	if($('#playervar .datarow').length < 2) return;
	$(this).closest('.datarow').remove();
});

$('#playervar [name=up]').click(function () {
	var item = $(this).closest('.datarow');
	var prev = $(item).prev('.datarow');
	if(prev.length) $(prev).before($(item));
});

});
</script>
</div>

<section>
<h5>Missing tracks</h5>
<?php 
foreach($event_tracks as $cat) {
	$cat_missing = [];
	foreach($cat['tracks'] as $exe=>$entry_nums) {
		$exe_missing = [];
		foreach($entry_nums as $entry_num) {
			if(empty($player_tracks[$exe])) {
				$check = 0;
			}
			else {
				$check = in_array($entry_num, $player_tracks[$exe]);
			}
			if(!$check) $exe_missing[] = $entry_num;
		}
		if($exe_missing) $cat_missing[] = sprintf('%s : %s', $exe, implode(' ', $exe_missing));
	}
	if($cat_missing) {
		printf('<p><strong>%s</strong><br>%s</p>', $cat['title'], implode('<br>', $cat_missing));
	}
} 
?>
</section>
<?php $this->endSection(); 

$this->section('bottom'); ?>
<div class="toolbar">
	<?php echo \App\Libraries\View::back_link("control/player/view/{$event->id}");?>
	<button class="btn btn-primary" type="button" name="update">save</button>
</div>
</form>
<?php $this->endSection(); 