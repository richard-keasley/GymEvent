<?php $this->extend('default');
use \App\Libraries\Teamtime as tt_lib;

$this->section('content'); 
$attr = [
	'id' => "editform",
	'style' => "max-width:45em;"
];
$hidden = [
	'save' => 1
];
echo form_open(current_url(), $attr, $hidden);
$evt_model = new \App\Models\Events();
$events = $evt_model->orderBy('date')->findAll();
$event_opts = [];
foreach($events as $event) { 
	$date = new DateTime($event->date);
	$event_opts[$event->id] = sprintf('%s: %s', $date->format('j-M-y'), $event->title);
}
$player_opts = ['local'=>'local', 'remote'=>'remote'];

$inputs = [
'event_id' => [
	'type' => 'select',
	'class' => 'form-control',
	'value' => tt_lib::get_value('settings', 'event_id'),
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
]
];

foreach($inputs as $key=>$input) { ?>
	<div class="my-1 row">
		<?php
		$input['id'] = "ctrl-$key";
		$input['name'] = $key;
		$label = humanize($key);
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
			default:
				if(is_array($input['value'])) $input['value'] = implode(', ', $input['value']);
				echo form_input($input);
		} ?>
		</div>
	</div>
	<?php
} 

echo form_close();

$count = count(tt_lib::get_images());	
$event_id = $inputs['event_id']['value'];
$image_path = "/public/events/{$event_id}/teamtime";
if($count) {
	$format = '<p class="alert alert-success">%s images in %s</p>';
	printf($format, $count, $image_path);
} else {
	$format = '<p class="alert alert-danger">No images found in %s!</p>';
	printf($format, $image_path);
}

$this->endSection(); 

$this->section('top'); ?>
<div class="toolbar sticky-top">
<?php echo \App\Libraries\View::back_link('control/teamtime'); ?>
<button class="btn btn-primary" type="submit" form="editform">save</button>
</div>
<?php $this->endSection(); 
