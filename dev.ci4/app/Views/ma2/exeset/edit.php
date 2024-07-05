<?php $this->extend('default');
 
$this->section('content'); ?>
<form id="editform">
<select class="form-select" name="idx" onchange="idxsel.reload();"></select>

<section>
<div class="input-group my-1">
	<label class="input-group-text" for="name" style="width:7em">Name</label>
	<?php
	$input = [
		'name' => "name",
		'id' => "name",
		'class' => "form-control",
		// 'value' => $exeset->name
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
		'options' => \App\Libraries\Mag\Rules::index,
		'onchange' => "rulsetname_change(this)"
	];
	echo form_dropdown($input);
?>
<script>
function rulsetname_change(el) {
	rulesetname = $(el).val(); // get new rule set
	console.log('switch to ' + rulesetname);
	var exeset = exesets.formdata.get(); // existing form data
	exeset[rulesetname] = rulesetname; // change to new rule set
	exesets.formdata.set(exeset); // apply new template to exeset
}
</script>
</div>

<div class="input-group my-1">
	<label class="input-group-text" for="event" style="width:7em">Event</label>
	<?php
	$input = [
		'class' => "form-control", 
		'id' => "event",
		'name' => "event",
		'style' => "height:4em",
		// 'value' => $exeset->event
	];
	echo form_textarea($input);
?></div>
</section>

<section id="edit-template"></section>

<div class="toolbar">
<?php
$buttons = [
	[
		'class' => "btn btn-primary bi bi-check-square",
		'title' => "Re-check all start values after edits",
		'type' => "button",
		'onclick' => "exesets.update()",
	],
	[
		'class' => "btn btn-primary bi bi-printer",
		'title' => "Printer friendly version of this exercise set",
		'href' => "/ma2/routine/print",
	],
	[
		'class' => "btn btn-primary bi bi-plus-square",
		'title' => "Make a copy of this exercise set to use on another gymnast",
		'type' => "button",
		'onclick' => "exesets.clone()"
	],
	[
		'class' => "btn btn-primary bi bi-tools",
		'title' => "Data utilities",
		'type' => "button",
		'data-bs-toggle' => "modal",
		'data-bs-target' => "#utils"
	],
	[
		'class' => "btn btn-danger bi-trash",
		'title' => "Delete this gymnast",
		'type' => "button",
		'data-bs-toggle' => "modal",
		'data-bs-target' => "#delentry"
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
	$href = $button['href'] ?? false;
	$format = $href ? '<a %s></a> ' : '<button %s></button> ' ;
	printf($format, stringify_attributes($button));
	$tbody[] = [
		sprintf('<span class="%s"></span>', $button['class']), 
		$button['title']
	];
}
?>

</div>

<?php echo form_close();?>

<?php $this->endSection(); 

$this->section('bottom');

echo '<script>const exesets_tmpl={};</script>';
foreach(\App\Libraries\Mag\Rules::index as $key=>$label) {
	$data = ['rulesetname' => $key];
	$data = ['exeset' => new \App\Libraries\Mag\Exeset($data)];
	echo view('ma2/exeset/edit-template', $data);
}

?>

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

<p class="text-muted">Rules' version: [Need to update this]<?php 
	// $time = new \CodeIgniter\I18n\Time($exeset->ruleset->version);
	// echo $time->toLocalizedString('d MMM yyyy'); 
?></p>

</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
</div>
</div>
</div>

<div class="modal" id="delentry" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
	<h5 class="modal-title">Delete <span class="entname"></span></h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
	<p>Are you sure you want to delete <span class="entname"></span>?</p>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
	<button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="exesets.delete()">Delete</button>
</div>
</div>
</div>
</div>

<div class="modal" id="utils" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title">Utilities</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
<?php
$buttons = [
[
	'class' => "btn btn-primary bi bi-arrow-down-square",
	'title' => "Save all data to your computer so it can be used on another device",
	'type' => "button",
	'onclick' => "alert('not yet')",
],
[
	'class' => "btn btn-primary bi bi-arrow-up-square",
	'title' => "Upload data from your computer",
	'type' => "button",
	'onclick' => "alert('not yet')",
],
[
	'class' => "btn btn-danger bi bi-trash",
	'title' => "Delete all data on this device",
	'type' => "button",
	'onclick' => "alert('not yet')",
],
];
$tbody = [];
foreach($buttons as $button) {
	$tbody[] = [
		sprintf('<button %s></button> ', stringify_attributes($button)),
		$button['title'] . '.'
	];
}
$table = \App\Views\Htm\Table::load('small');
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

<script><?php
ob_start();
include __DIR__ . '/exesets.js';
echo ob_get_clean();
/*

$minifier = new MatthiasMullie\Minify\JS();
$minifier->add(ob_get_clean());
echo $minifier->minify();
*/
?>

$(function() {

document.getElementById('delentry').addEventListener('show.bs.modal', function(event) {
	let entname = $('#editform [name=name]').val();
	$('#delentry .entname').html(entname);
});

exesets.idx = localStorage.getItem('mag-exesets-idx') ?? 0;
var exeset = exesets.storage.load();
exesets.formdata.set(exeset);
idxsel.init();

});
</script>

<?php
$this->endSection(); 
