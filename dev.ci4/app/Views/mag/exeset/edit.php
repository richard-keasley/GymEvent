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

<div class="row">
<div class="col-auto">Saved: <?php 
	$time = new \CodeIgniter\I18n\Time($exeset->saved);
	echo $time->toLocalizedString('d MMM yyyy'); 
?></div>
<div class="col-auto">Rules' version: <?php 
	$time = new \CodeIgniter\I18n\Time($exeset->ruleset->version);
	echo $time->toLocalizedString('d MMM yyyy'); 
?></div>
</div>

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
		'style' => "max-height:4em",
		'value' => $exeset->event
	];
	echo form_textarea($input);
?></div>

<?php 
$exeval_fields = ['rulesetname']; // only these fields sent to API
$tab_items = [];
foreach($exeset->exercises as $exekey=>$exercise) {
	ob_start();
	
	$exe_rules = $exeset->ruleset->exes[$exekey] ?? [] ;
	switch($exe_rules['method']) {
		case 'tariff':
			$inputs = [
				[
					'type' => 'text',
					'style' => "max-width:5em",
					'class' => "form-control",
					'placeholder' => 'tariff'
				],
				[
					'type' => 'select',
					'options' => $exeset->ruleset->routine_options('groups'),
					'style' => "max-width:4em",
					'class' => "form-control",
					'placeholder' => 'grp'
				],
				[
					'type' => 'text',
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
					'type' => 'select',
					'options' => $exeset->ruleset->routine_options('difficulties'),
					'style' => "max-width:3em",
					'class' => "form-control",
					'placeholder' => 'val'
				],
				[
					'type' => 'select',
					'options' => $exeset->ruleset->routine_options('groups'),
					'style' => "max-width:3em",
					'class' => "form-control",
					'placeholder' => 'grp'
				],
				[
					'type' => 'text',
					'class' => "form-control",
					'placeholder' => 'description'
				]
			];
			$dismount_num = array_key_last($exercise['elements']); 
	}
	foreach($exercise['elements'] as $elnum=>$element) { 
		?>
		<div class="input-group my-0">
		<span class="input-group-text" style="width:3em">
			<?php echo $elnum==$dismount_num ? 'D' : $elnum + 1; ?>
		</span>
		<?php
		foreach($inputs as $col=>$input) {
			$input['name'] = "{$exekey}_el_{$elnum}_{$col}";
			$input['value'] = $element[$col];
			if($col<2) $exeval_fields[] = $input['name'];
			switch($input['type']) {
				case 'select': 
					unset($input['type']);
					echo form_dropdown($input);
					break;
				default:
					echo form_input($input);
			}
		}
		?>
		</div>
	<?php }
	
	if(!empty($exe_rules['connection'])) { ?>
		<div class="input-group my-1">
		<span class="input-group-text" style="width:7em">Connection</span>
		<?php 
		$id = "{$exekey}_con";
		$input = [
			'type' => "text",
			'class' => "form-control",
			'name' => $id,
			'id' => $id,
			'value' => $exercise['connection'] ?? 0
		];
		$exeval_fields[] = $input['name'];
		echo form_input($input);
		?>
		</div>
	<?php } ?>
	
	<div class="my-2">
	<?php foreach($exercise['neutrals'] as $nkey=>$nval) { ?>
		<div class="form-check">
		<?php
		$id = "{$exekey}_nd_{$nkey}";
		$exeval_fields[] = $id;
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
	<?php } ?>
	</div>
	
	<div id="exeval-<?php echo $exekey;?>">
	<?php echo view('mag/exeset/exeval', ['exekey'=>$exekey, 'exeset'=>$exeset]); ?>
	</div>
	
	<?php 
	$tab_items[] = [
		'heading' => $exe_rules['name'],
		'content' => ob_get_clean()
	];
}
$tabs = new \App\Libraries\Ui\Tabs($tab_items);
echo $tabs->htm();
?>
<div class="toolbar">
<button class="btn btn-primary bi bi-check-square" title="re-check this routine after edits" type="button" name="update"> update</button>
<button class="btn btn-primary bi bi-arrow-down-square" title="save these routines to your computer so they can be altered later" type="submit" name="cmd" value="store"> save</button>
<button class="btn btn-primary bi bi-printer" title="print this routine sheet" type="submit" name="cmd" value="print"> print</button>
<button class="btn btn-primary bi bi-plus-square" title="make a copy of this routine sheet to use on another gymnast" type="button" name="clone"> clone</button>
</div>
<script>
const api = '<?php echo base_url("/api/mag/exeval");?>/';

$('#editform button[name=clone]').click(function() {
	var form = $('#editform')[0];
	var name_field = $('#editform [name=name]');
	var name = name_field.val();
	form.target = '_blank';
	name_field.val('copied');
	$('#editform').submit();
	form.target = '_self';
	name_field.val(name);
});

$('#editform button[name=update]').click(function() {
	get_exevals();
});

$('#editform [name=rulesetname]').change(function() {
	$('#editform').submit();
});

$(function() {
	if('serviceWorker' in navigator) {
		navigator.serviceWorker.register('/api/mag/exeval');
	}
});
const exekeys = <?php echo json_encode(array_keys($exeset->exercises));?>;
const exeval_fields = <?php echo json_encode($exeval_fields);?>;

function get_exevals() {
	var name = $('[name=name]').val().trim();
	if(name) { 
		$('h1').html(name);
		document.title = name;
	}
	
	var exeset = {}; var el = null; var val = null;
	exeval_fields.forEach(fld => {
		el = $('[name='+fld+']')[0];
		switch(el.type) {
			case 'checkbox':
				val = el.checked ? 1 : 0 ;
				break;
			default:
				val = el.value;
		}
		exeset[fld] = val;
	})

	$.get(api, exeset, function(response) {
		try { 
			update_exevals(response, 1); 
		}
		catch(errorThrown) { 
			update_exevals(errorThrown);
		}
	})
	.fail(function(jqXHR) {
		update_exevals('server error');
	});
}

function update_exevals(message, message_ok=0) {
	let htm = ''; this_ok = 0;
	exekeys.forEach(function(exekey) {
		this_ok = message_ok ? typeof(message[exekey])!="undefined" : 0 ;
		if(this_ok) {
			htm = message[exekey];
		}
		else {
			htm = message_ok ? exekey + ' missing in response' : message ;
		}
		if(!this_ok) htm = '<ul class="list-unstyled alert-danger"><li>API error: ' + htm + '.</li></ul>';
		$('#exeval-'+exekey).html(htm);
	});
}

</script>
<?php
echo form_close();

# d($exeset);
# d($exeval_fields);

$this->endSection(); 

