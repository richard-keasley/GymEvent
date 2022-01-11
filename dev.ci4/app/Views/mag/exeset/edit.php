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
<section>
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
		'options' => \App\Libraries\Mag\Rules::index
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
		'style' => "height:4em",
		'value' => $exeset->event
	];
	echo form_textarea($input);
?></div>
</section>

<section>
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
					'type' => "number",
					'step' => "0.1",
					'min' => "0",
					'max' => $exe_rules['d_max'],
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

	$last_elnum = array_key_last($exercise['elements']); 
	foreach($exercise['elements'] as $elnum=>$element) {
		$start_style = 'width:3em;';
		$end_style = '';
		if($elnum!=0) {
			$start_style .= ' border-top-left-radius:0;';
			$end_style .= ' border-top-right-radius:0;';
		}
		if($elnum!=$last_elnum)  {
			$start_style .= ' border-bottom-left-radius:0;';
			$end_style .= ' border-bottom-right-radius:0;';
		}
		?>
		<div class="input-group my-0">
		<span class="input-group-text" style="<?php echo $start_style;?>">
			<?php echo $elnum==$dismount_num ? 'D' : $elnum + 1; ?>
		</span>
		<?php
		foreach($inputs as $col=>$input) {
			$input['name'] = "{$exekey}_el_{$elnum}_{$col}";
			$input['value'] = $element[$col];
			if($col<2) $exeval_fields[] = $input['name'];
			if($col==array_key_last($inputs)) $input['style'] = $end_style;
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
			'type' => "number",
			'step' => "0.1",
			'min' => "0",
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
	$tab_items[$exekey] = [
		'heading' => $exe_rules['name'],
		'content' => ob_get_clean()
	];
}

$tabs = new \App\Views\Htm\Tabs($tab_items, 'exes');
echo $tabs->htm();
?>
</section>

<div class="toolbar">
<?php
$buttons = [
	[
		'class' => "btn btn-primary bi bi-check-square",
		'title' => "Re-check all start values after edits",
		'type' => "button",
		'name' => "update"
	],
	[
		'class' => "btn btn-primary bi bi-arrow-down-square",
		'title' => "Save this exercise set to your computer so the routines can be altered later",
		'type' => "submit",
		'name' => "cmd",
		'value' => "store"
	],
	[
		'class' => "btn btn-primary bi bi-printer",
		'title' => "Printer friendly version of this exercise set",
		'type' => "submit",
		'name' => "cmd",
		'value' => "print"
	],
	[
		'class' => "btn btn-primary bi bi-plus-square",
		'title' => "Make a copy of this exercise set to use on another gymnast",
		'type' => "button",
		'name' => "clone"
	],
	[
		'class' => "btn btn-danger bi-trash",
		'title' => "Clear the exercise currently visible",
		'type' => "button",
		'data-bs-toggle' => "modal",
		'data-bs-target' => "#execlear"
	]	
];

$tbody = [];
foreach($buttons as $button) {
	printf('<button %s></button> ', stringify_attributes($button));
	$tbody[] = [
		sprintf('<span class="%s"></span>', $button['class']), 
		$button['title'] . '.'
	];
}
?>
<button type="button" title="Button help" class="btn btn-info bi-question-circle" data-bs-toggle="modal" data-bs-target="#iconhelp"></button>
</div>

<div class="modal" id="iconhelp" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
	<h5 class="modal-title">Button functions</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
<?php 
$table = new \CodeIgniter\View\Table();
$template = ['table_open' => '<table class="table table-sm">'];
$table->setTemplate($template);	
$table->autoHeading = false;
echo $table->generate($tbody);
?>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
</div>
</div>
</div>

<?php echo form_close();

# d($exeset);
# d($exeval_fields);
$this->endSection(); 

$this->section('top') ?>

<div class="modal" id="execlear" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
	<h5 class="modal-title">Reset <span class="exename"></span></h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
	<p>Are you sure you want to clear contents from the <span class="exename"></span> exercise?</p>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
	<button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="execlear()">Clear exercise</button>
</div>
</div>
</div>
</div>

<script>
const api = '<?php echo base_url("/api/mag/exeval");?>/';
const filter = <?php 
	$arr = [];
	foreach(\App\Libraries\Mag\Exeset::filter as $key=>$val) {
		$arr[] = [$key, $val];	
	}
	echo json_encode($arr);
?>;
const exekeys = <?php echo json_encode(array_keys($exeset->exercises));?>;
const exeval_fields = <?php echo json_encode($exeval_fields);?>;
let execlearModal = null;
let exename = null;

<?php /*

if('serviceWorker' in navigator) {
	navigator.serviceWorker.register('/mag/routineSW', {scope: '/mag/'})
	.then((reg) => {
		// registration worked
		console.log('Registration succeeded. Scope is ' + reg.scope);
	}).catch((error) => {
		// registration failed
		console.log('Registration failed with ' + error);
	});
}
*/ ?>

$(function() {

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

execlearModal = document.getElementById('execlear');
execlearModal.addEventListener('show.bs.modal', function (event) {
	exename = $('#exes .nav-tabs .active').html();
	$('#execlear .exename').html(exename);
});

});

function execlear(exekey) {
	$('#exes .tab-pane.active select').val('');
	$('#exes .tab-pane.active input[type=text]').val('');
	$('#exes .tab-pane.active input[type=number]').val(0);
	$('#exes .tab-pane.active input[type=checkbox]').prop("checked", false);
	get_exevals();
}

function get_exevals() {
	// work out title from gymnast's name
	var name = $('[name=name]').val().trim();
	filter.forEach((element) => {
		var search = new RegExp(element[0], "gi");
		name = name.replace(search, element[1]);
	});
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
	});

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

<?php $this->endSection(); 
