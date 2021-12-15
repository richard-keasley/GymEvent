<?php $this->extend('default');
 
$this->section('content');

$action = '';
$attr = [
	'id' => "editform",
	'data' => ''
];
$hidden = [
	'saved' => $exeset->saved
];
echo form_open_multipart(base_url(uri_string()), $attr, $hidden); 

?>
<div class="input-group my-1">
	<label class="input-group-text" for="name" style="width:7em">Name</label>
	<?php
	$input = [
		'name' => "name",
		'id' => "name",
		'class' => "form-control",
		'value' => $exeset->name
	];
	echo form_input($input);
	?>
</div>

<div class="input-group my-1">
	<label class="input-group-text" for="rulesetname" style="width:7em">Code</label>
	<?php
	$input = [
		'class' => "form-control", 
		'id' => "rulesetname",
		'name' => "rulesetname",
		'options' => \App\Libraries\Mag\Rules::index()
	];
	echo form_dropdown($input);
?></div>

<div class="input-group my-1">
	<label class="input-group-text" for="event" style="width:7em">Event</label>
	<?php
	$input = [
		'class' => "form-control", 
		'id' => "event",
		'name' => "event",
		'style' => "max-height:5em",
		'value' => $exeset->event
	];
	echo form_textarea($input);
?></div>

<?php 
$items = [];
foreach($exeset->exercises as $exekey=>$exercise) {
	ob_start();
	
	$exe_rules = $exeset->ruleset->exes[$exekey] ?? [] ;
	switch($exe_rules['method']) {
		case 'tariff':
			$inputs = [
				[
					'style' => "max-width:5em",
					'class' => "form-control",
					'placeholder' => 'tariff'
				],
				[
					'style' => "max-width:5em",
					'class' => "form-control",
					'placeholder' => 'grp',
					'type' => 'number'
				],
				[
					'name' => '',
					'class' => "form-control",
					'placeholder' => 'description'
				]
			];
			$dismount_num =  999;
			break;
		case 'routine':
		default: 
			$inputs = [
				[
					'style' => "max-width:3em",
					'class' => "form-control",
					'placeholder' => 'val'
				],
				[
					'style' => "max-width:5em",
					'class' => "form-control",
					'placeholder' => 'grp',
					'type' => 'number'
				],
				[
					'class' => "form-control",
					'placeholder' => 'description'
				]
			];
			$dismount_num = count($exercise['elements']) - 1; 
	}
	foreach($exercise['elements'] as $elnum=>$element) { 
		?>
		<div class="input-group my-1">
		<span class="input-group-text" style="width:3em">
			<?php echo $elnum==$dismount_num ? 'D' : $elnum + 1; ?>
		</span>
		<?php
		foreach($inputs as $col=>$input) {
			$input['name'] = "{$exekey}_el_{$elnum}_{$col}";
			$input['value'] = $element[$col];
			echo form_input($input);
		}
		?>
		</div>
	<?php }
	
	foreach($exercise['neutrals'] as $nkey=>$nval) { 
		?>
		<div class="form-check">
		<?php
		$id = "{$exekey}_n_{$nkey}";
		$input = [
			'type' => 'checkbox',
			'name' => $id,
			'id' => $id,
			'value' => 1,
			'class' => "form-check-input"
		];
		$label = [
			'class' => "form-check-label"
		];
		if($nval) $input['checked'] = 'checked';
		echo form_input($input);
		$neutral = $exe_rules['neutrals'][$nkey]; 
		echo form_label(sprintf('%s (%1.1f)', $neutral['description'], $neutral['deduction']), $id, $label);
		?>
		</div>
	<?php } 
	
	echo view('mag/exeset/exe_eval', ['exekey'=>$exekey, 'exeset'=>$exeset]);
	
	

	
	
		
	$items[] = [
		'heading' => $exe_rules['name'],
		'content' => ob_get_clean()
	];
}
$tabs = new \App\Libraries\Ui\Tabs($items);
echo $tabs->htm();

?>
<div class="toolbar">
<button class="btn btn-primary bi bi-printer" title="print this routine sheet" type="submit" name="view" value="print"> print</button>
<button class="btn btn-primary bi bi-journal-arrow-down" title="save these routines to your computer so they can be altered later" type="submit" name="view" value="store"> save</button>
<button class="btn btn-primary bi bi-journal-plus" title="make a copy of this routine sheet to use on another gymnast" type="button" name="clone"> clone</button>
<button class="btn btn-primary bi bi-journal-check" title="re-check this routine after edits" type="submit" name="view" value="edit"> update</button>
</div>
<script>
/*
$('#editform').submit(function(e) { 
	var data = $(this).serializeArray();
	//console.log(data);
	$('#editform [name=data]').val(JSON.stringify(data));
});
*/
</script>
<?php

d($exeset);

echo form_close();
$this->endSection(); 

