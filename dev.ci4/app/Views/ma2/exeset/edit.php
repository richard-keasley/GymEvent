<?php $this->extend('default');

$buttons = [
	'update' => [
		'class' => "btn btn-primary bi bi-check-square",
		'title' => "Re-check all start values after edits",
		'type' => "button",
		'onclick' => "exesets.update()",
	],
	'print' => [
		'class' => "btn btn-primary bi bi-printer",
		'title' => "Printer friendly version of this exercise set",
		'href' => "/ma2/routine/print",
	],
	'clone' => [
		'class' => "btn btn-primary bi bi-plus-square",
		'title' => "Make a copy of this exercise set to use on another gymnast",
		'type' => "button",
		'onclick' => "exesets.clone()"
	],
	'data' => [
		'class' => "btn btn-primary bi bi-file-code",
		'title' => "Data utilities",
		'type' => "button",
		'onclick' => "magexes.dlg_data()",
		# 'data-bs-toggle' => "modal"
	],
	'delete' => [
		'class' => "btn btn-danger bi-trash",
		'title' => "Delete this gymnast",
		'type' => "button",
		'onclick' => "magexes.dlg_delete()",
		'data-bs-toggle' => "modal"
	],
	'export' => [
		'class' => "btn btn-primary bi bi-arrow-down-square",
		'title' => "Save all data to your computer so it can be used on another device",
		'type' => "button",
		'onclick' => "magexes.save()",
		'data-bs-toggle' => "modal"
	],
	'import' => [
		'class' => "btn btn-primary bi bi-arrow-up-square",
		'title' => "Upload data from your computer",
		'href' => site_url("ma2/import"),
	],
	'clear' => [
		'class' => "btn btn-danger bi bi-trash",
		'title' => "Delete all data on this device",
		'type' => "button",
		'data-bs-toggle' => "modal",
		'data-bs-target' => "#dlg_clear"
	],
	'help' => [
		'class' => "btn btn-info bi-question-circle",
		'title' => "Button help",
		'type' => "button",
		'data-bs-toggle' => "modal",
		'data-bs-target' => "#dlg_help"
	]
];
foreach($buttons as $key=>$button) {
	$href = $button['href'] ?? false;
	$format = $href ? '<a %s></a>' : '<button %s></button>' ;
	$buttons[$key] = [
		'button' => sprintf($format, stringify_attributes($button)),
		'title' => $button['title'] ?? $key
	];
}
 
$this->section('content'); 
$attrs = ['id'=>"editform"];
echo form_open('', $attrs);

$idxsel = $buttons['print']['button'];
include __DIR__ . '/idxsel.php';
?>

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

<div class="toolbar"><?php
$keys = ['update', 'clone', 'data', 'delete', 'help'];
foreach($keys as $key) echo $buttons[$key]['button'];
?></div>

<?php 
echo form_close();
$this->endSection(); 

$this->section('bottom');

echo '<script>const exesets_tmpl={};</script>';
foreach(\App\Libraries\Mag\Rules::index as $key=>$label) {
	$data = ['rulesetname' => $key];
	$data = ['exeset' => new \App\Libraries\Mag\Exeset($data)];
	echo view('ma2/exeset/edit-template', $data);
}

?>

<div class="modal" id="dlg_help" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
	<h5 class="modal-title">Button functions</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
<?php 
$keys = ['print', 'update', 'clone', 'delete', 'data'];
$tbody = [];
foreach($keys as $key) {
	$tbody[] = $buttons[$key];
}
$table = \App\Views\Htm\Table::load('small');
$table->autoHeading = false;
echo $table->generate($tbody);
?>

<h6>Rule set</h6>
<p class="text-muted" id="help-ruleset"></p>

</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
</div>
</div>
</div>

<div class="modal" id="dlg_delete" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
	<h5 class="modal-title">Delete <span class="entname"></span></h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
	<p>Are you sure you want to delete '<span class="entname"></span>'?</p>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
	<button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="exesets.delete()">Delete</button>
</div>
</div>
</div>
</div>

<div class="modal" id="dlg_clear" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
	<h5 class="modal-title">Delete all data</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
	<p>Are you sure you want to delete <strong>all data</strong> held on this device?</p>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
	<button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="magexes.clear()">Delete</button>
</div>
</div>
</div>
</div>

<div class="modal" id="dlg_data" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title">Manage data</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
<?php
$keys = ['export', 'import', 'clear'];
$tbody = [];
foreach($keys as $key) {
	$tbody[] = $buttons[$key];
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

<?php 
$attrs = ['id' => "magexes-save"];
$hidden = ['exesets' => ''];
echo form_open('ma2/export', $attrs, $hidden);
echo form_close();

?>

<script><?php
ob_start();
include __DIR__ . '/exesets.js';
?>

const magexes = {
save: function() {
	var data = exesets.storage.get();
	$('#magexes-save [name=exesets]').val(JSON.stringify(data));
	$('#magexes-save').submit();		
},
clear: function() {
	exesets.storage.set([]);
	localStorage.setItem('mag-exesets-idx', 0);	
	window.location.assign('<?php echo base_url('ma2/routine');?>');
},
dlg_data: function() {
	exesets.update();
	var modal = new bootstrap.Modal('#dlg_data');
	modal.show();
},
dlg_delete: function() {
	var modal = new bootstrap.Modal('#dlg_delete');
	var entname = $('#editform [name=name]').val();
	$('#dlg_delete .entname').html(entname);
	modal.show();
}

}

$(function() {

exesets.idx = localStorage.getItem('mag-exesets-idx') ?? 0;
var exeset = exesets.storage.load();
exesets.formdata.set(exeset);

});

<?php 
echo ob_get_clean();
/*

$minifier = new MatthiasMullie\Minify\JS();
$minifier->add(ob_get_clean());
echo $minifier->minify();
*/
?>
</script>

<?php
$this->endSection(); 
