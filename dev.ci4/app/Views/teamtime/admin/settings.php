<?php $this->extend('default');
use \App\Libraries\Teamtime as tt_lib;
$event_id = tt_lib::get_value('settings', 'event_id');

$this->section('content'); 

$attrs = [
	'id' => "editform",
	'style' => "max-width:45em;"
];
$hidden = [
	'save' => "1"
];
echo form_open('', $attrs, $hidden);
$evt_model = new \App\Models\Events;
$events = $evt_model->where('clubrets >', 1)->orderBy('date')->findAll();
$event_opts = [];
foreach($events as $event) {
	$date = (new \DateTime($event->date))->format('j-M-y');
	$event_opts[$event->id] = "{$date}: {$event->title}";
}
$player_opts = ['local'=>'local', 'sender'=>'sender'];


$inputs = [
'event_id' => [
	'type' => 'select',
	'class' => 'form-control',
	'value' => $event_id,
	'options' => $event_opts
],
'music_player' => [
	'type' => 'select',
	'class' => 'form-control',
	'value' => tt_lib::get_value('settings', 'music_player'),
	'options' => $player_opts
],
'run_rows' => [
	'type' => 'text',
	'class' => 'form-control',
	'value' => tt_lib::get_value('settings', 'run_rows')
],
];

foreach($inputs as $key=>$input) { ?>
	<div class="my-1 row">
		<?php
		$input['id'] = "ctrl-$key";
		$input['name'] = $key;
		$label = match($key) {
			'event_id' => "Event ({$event_id})",
			default => humanize($key)
		};
		$class = $input['type']=='checkbox' ? 'col-form-check-label' : 'col-form-label' ;
		printf('<label for="%s" class="col-sm-3 text-end %s">%s</label>', $input['id'], $class, $label); 
		?>
		<div class="col-sm-9">
		<?php switch($input['type']) {
			case 'select':
			$input['selected'] = $input['value'];
			unset($input['type'], $input['value']);
			echo form_dropdown($input);
			break;
			
			case 'readonly':
			printf('<div class="%s">%s</div>', $input['class'], $input['value']);
			break;
			
			default:
			if(is_array($input['value'])) $input['value'] = implode(', ', $input['value']);
			echo form_input($input);
		} ?>
		</div>
	</div>
	<?php
}

echo form_close();
$this->endSection(); 

$this->section('bottom'); 

$images = tt_lib::get_images();
$count = count($images);
$image_path = "/public/events/{$event_id}/teamtime";

if($count) { ?>
<div class="border p-1 my-1">
<p>Image path: <code><?php echo $image_path;?></code></p>
<div class="row"><?php 
$format = '<div class="col-auto" style="max-width:12em"><img src="%s" class="my-1"></div>';
foreach($images as $src) printf($format, $src);
?></div>
</div>
<?php }

else { ?>
<p class="alert alert-danger">No images found in <?php echo $image_path;?>!</p>
<?php }

$this->endSection(); 

$this->section('top'); ?>
<div class="toolbar sticky-top">
<?php echo \App\Libraries\View::back_link('control/teamtime'); ?>
<button class="btn btn-primary" type="submit" form="editform">save</button>
</div>
<?php $this->endSection(); 
