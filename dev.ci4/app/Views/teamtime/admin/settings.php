<?php $this->extend('default');

$this->section('content'); 
$attr = [
	'id' => "editform"
];
$hidden = [
	'save' => 1
];
echo form_open(base_url(uri_string()), $attr, $hidden);
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
	'value' => $tt_lib::get_var('settings', 'event_id'),
	'options' => $event_opts
],
'music_player' => [
	'type' => 'select',
	'class' => 'form-control',
	'value' => $tt_lib::get_var('settings', 'music_player'),
	'options' => $player_opts
],
'image_path' => [
	'type' => 'text',
	'class' => 'form-control',
	'value' => $tt_lib::get_var('settings', 'image_path')
],
'run_rows' => [
	'type' => 'text',
	'class' => 'form-control',
	'value' => $tt_lib::get_var('settings', 'run_rows')
]
];

foreach($inputs as $key=>$input) { ?>
	<div class="my-1 row"><?php
		$input['id'] = "ctrl-$key";
		$input['name'] = $key;
		$label = humanize($key);
		$class = $input['type']=='checkbox' ? 'col-form-check-label' : 'col-form-label' ;
		printf('<label for="%s" class="col-sm-3 %s">%s</label>', $input['id'], $class, $label); ?>
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
$this->endSection(); 

$this->section('top'); ?>
<div class="toolbar sticky-top">
<?php echo \App\Libraries\View::back_link('control/teamtime'); ?>
<button class="btn btn-primary" type="submit" form="editform">save</button>
</div>
<?php $this->endSection(); 
