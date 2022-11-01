<?php $this->extend('default'); 
$player = $event->player;
if(!$player) $player = [\App\Entities\Event::player_row];

$this->section('content');
# d($event);
# d($player);
# $track = new \App\Libraries\Track;
# $track->event_id = $event->id;

$attr = ['id'=>'editform'];
$hidden = [
	'view' => "admin",
	'player' => "",
	'cmd' => 'update'
];
echo form_open(current_url(), $attr, $hidden);
?>
<div class="container table-responsive">
<div id="playervar" style="min-width:20em;">

<?php foreach($player as $round) { ?>
<div class="datarow row row-fluid py-2 border-bottom">

<div class="col-4"><?php
	$input = [
		'class' => "form-control",
		'data-name' => "title",
		'placeholder' => "title",
		'value' => $round['title']
	];
	echo form_input($input);
	
	$input = [
		'class' => "form-control",
		'data-name' => "exe",
		'placeholder' => "exercise",
		'value' => $round['exe']
	];
	echo form_input($input);
	
	$input = [
		'class' => "form-control",
		'data-name' => "description",
		'placeholder' => "description",
		'value' => $round['description']
	];
	echo form_input($input);
	
?></div>

<div class="col-6">
	<textarea class="form-control" rows="3" data-name="entry_nums" placeholder="Exercise entry numbers"><?php echo implode(' ', $round['entry_nums']);?></textarea>
</div>

<div class="col-2">
	<div class="btn-group-vertical">
	<button type="button" name="up" class="btn bi-arrow-up-circle btn-info"></button>
	<button type="button" name="del" class="btn bi-trash btn-danger" title="delete"></button>
	</div>
</div>

</div>
<?php } ?>

<button name="add" type="button" class="btn bi-plus-square btn-success" title="add round"></button>
<script>
const fields = ['exe','title','description','entry_nums'];
$(function() {
$('[name=update]').click(function() {
	var player = []; 
	$('#playervar .datarow').each(function() {
		var datarow = this, player_row = {};
		fields.forEach(function(item, index) {
			player_row[item] = $(datarow).find('[data-name='+item+']').val().trim();
		});
		player.push(player_row);
	});
	$('[name=player]').val(JSON.stringify(player));
	$('#editform').submit();
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
</div>

<section>
<h5>Unlisted tracks</h5>
<?php 
// all tracks listed on player
$player_tracks = []; 
foreach($player as $round) {
	$exekey = strtolower($round['exe']);
	if(empty($player_tracks[$exekey])) $player_tracks[$exekey] = [] ;
	$player_tracks[$exekey] = array_merge($round['entry_nums'], $player_tracks[$exekey]);
}
# d($player_tracks);
# d($entries);

foreach($entries as $cat) {
	$exe = strtolower($cat['exe']);
	$exe_missing = [];
	foreach($cat['entries'] as $entry) {
		if(empty($player_tracks[$exe])) {
			$check = 0;
		}
		else {
			$check = in_array($entry['num'], $player_tracks[$exe]);
		}
		if(!$check) $exe_missing[] = $entry['num'];
	}
	if($exe_missing) {
		printf('<p><strong>%s %s - %s</strong><br>%s</p>', $cat['dis'], $cat['cat'], $cat['exe'], implode(' ', $exe_missing));
	}	
} 
?>
</section>
<?php 
echo form_close();
$this->endSection(); 

$this->section('bottom'); ?>
<div class="toolbar">
	<?php echo \App\Libraries\View::back_link("control/player/view/{$event->id}");?>
	<button class="btn btn-primary" type="button" name="update">save</button>
	<button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#upload" title="download track from live website"><i class="bi bi-download"></i></button>
	<button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#rebuild" title="rebuild play list"><i class="bi bi-arrow-repeat"></i></button>
</div>

<div class="modal fade" id="rebuild" tabindex="-1" aria-hidden="true">
<div class="modal-dialog">
<?php 
$attr = ['class' => "modal-content"];
$hidden = ['cmd'=>'rebuild'];
echo form_open(current_url(), $attr, $hidden);
?>

<div class="modal-header">
<h5 class="modal-title" id="exampleModalLabel">Rebuild play track list</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
<p>Clear all entries from the player and re-build from running order?</p>
</div>

<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
<button type="submit" class="btn btn-primary">Rebuild</button>
</div>

<?php echo form_close();?>
</div>
</div>

<div class="modal fade" id="upload" tabindex="-1" aria-hidden="true">
<div class="modal-dialog">
<?php 
$attr = ['class' => "modal-content"];
$hidden = [];
echo form_open_multipart(current_url(), $attr, $hidden);
?>

<div class="modal-header">
<h5 class="modal-title" id="exampleModalLabel">Download track from remote website</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
<p>Pull a track from the live website to this device.</p>
<p class="alert alert-warning p-1">This will overwrite existing tracks for this entry / exercise on this device.</p>
<div class="my-1"><div class="input-group">
	<label class="input-group-text">Number</label> 
	<?php 
	$input = [
		'name' => 'entry_num',
		'type' => 'number',
		'class' => "form-control"
	];
	echo form_input($input);
	?>
</div></div>
<div class="my-1"><div class="input-group">
	<label class="input-group-text">Exercise</label> 
	<?php 
	$options = ['-'];
	foreach($player as $round) {
		$exe = strtoupper($round['exe']);
		$options[$exe] = $exe;
	}
	$input = [
		'name' => 'exe',
		'class' => "form-control",
		'options' => $options
	];
	echo form_dropdown($input);
	?>
</div></div>
<p>Ensure uploaded tracks can be played.</p>
</div>

<div class="modal-footer">
<button type="submit" name="cmd" value="synch" class="btn btn-primary" title="Pull track from remote source">synch</button>
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>

<?php echo form_close();?>
</div>
</div>
<?php $this->endSection(); 
