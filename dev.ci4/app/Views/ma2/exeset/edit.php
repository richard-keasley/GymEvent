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
echo form_open_multipart(current_url(), $attr, $hidden); 
?>
<section>
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
$exeval_fields = [
	'rulesetname' => "rulesetname", 
	'name' => "name", 
	'event' => "event"
]; 
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
					'class' => "form-control tarrif-0",
					'placeholder' => 'tariff'
				],
				[
					'type' => 'select',
					'options' => $exeset->ruleset->routine_options('groups'),
					'class' => "form-control tarrif-1",
					'placeholder' => 'grp'
				],
				[
					'type' => 'text',
					'class' => "form-control tarrif-2",
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
					'class' => "form-control routine-0",
					'placeholder' => 'val'
				],
				[
					'type' => 'select',
					'options' => $exeset->ruleset->routine_options('groups'),
					'class' => "form-control routine-1",
					'placeholder' => 'grp'
				],
				[
					'type' => 'text',
					'class' => "form-control routine-2",
					'placeholder' => 'description'
				]
			];
			$dismount_num = array_key_last($exercise['elements']); 
	}

	$last_elnum = array_key_last($exercise['elements']); 
	foreach($exercise['elements'] as $elnum=>$element) {
		$class = '';
		if($elnum) $class .= ' not-first';
		if($elnum<$last_elnum) $class .= ' not-last';
		?>
		<div class="input-group my-0">
		<span class="input-group-text elnum<?php echo $class;?>">
			<?php echo $elnum==$dismount_num ? 'D' : $elnum + 1; ?>
		</span>
		<?php
		foreach($inputs as $col=>$input) {
			$input['name'] = "{$exekey}_el_{$elnum}_{$col}";
			$input['value'] = $element[$col];
			$input['class'] .= $class;
			$exeval_fields[$exekey]['el'][$elnum][$col] = $input['name'];

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
		$exeval_fields[$exekey]['con'] = $input['name'];
		echo form_input($input);
		?>
		</div>
	<?php } ?>
	
	<div class="my-2">
	<?php foreach($exercise['neutrals'] as $nkey=>$nval) { ?>
		<div class="form-check">
		<?php
		$id = "{$exekey}_nd_{$nkey}";
		$input = [
			'type' => 'checkbox',
			'name' => $id,
			'id' => $id,
			'value' => 1,
			'class' => "form-check-input"
		];
		if($nval) $input['checked'] = 'checked';
		$exeval_fields[$exekey]['nd'][$nkey] = $input['name'];
		$neutral = $exe_rules['neutrals'][$nkey]; 
		
		$attr = [
			'class' => "form-check-label"
		];
	
		echo form_input($input);
		echo form_label(sprintf('%s (%1.1f)', $neutral['description'], $neutral['deduction']), $id, $attr);
		?>
		</div>
	<?php } ?>
	</div>
	
	<div id="exeval-<?php echo $exekey;?>">
	<?php 
	$this->setData(['exekey' => $exekey]);
	echo $this->include('ma2/exeset/exeval'); 
	?>
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
	],
	[
		'class' => "btn btn-info bi-question-circle",
		'title' => "Button help",
		'type' => "button",
		'data-bs-toggle' => "modal",
		'data-bs-target' => "#iconhelp"
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

</div>

<?php echo form_close();

# d($exeset);
 d($exeval_fields);
$this->endSection(); 

$this->section('bottom') ?>

<div class="modal" id="iconhelp" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
	<h5 class="modal-title">Button functions</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
<?php 
$table = \App\Views\Htm\Table::load('small');
$table->autoHeading = false;
echo $table->generate($tbody);
?>

<p class="text-muted">Rules' version: <?php 
	$time = new \CodeIgniter\I18n\Time($exeset->ruleset->version);
	echo $time->toLocalizedString('d MMM yyyy'); 
?></p>

</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
</div>
</div>
</div>

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
<?php
 d($exeset);


?>
<script><?php
ob_start();
include __DIR__ . '/edit.js';
echo ob_get_clean();
/*

$minifier = new MatthiasMullie\Minify\JS();
$minifier->add(ob_get_clean());
echo $minifier->minify();
*/
?></script>

<?php $this->endSection(); 
