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
		'title' => "Printer friendly version of these routines",
		'href' => "/ma2/routine/print",
	],
	'clone' => [
		'class' => "btn btn-primary bi bi-plus-square",
		'title' => "Copy these routines to use on another gymnast",
		'type' => "button",
		'onclick' => "exesets.clone()"
	],
	'data' => [
		'class' => "btn btn-primary bi bi-file-code",
		'title' => "Data utilities",
		'type' => "button",
		'onclick' => "magexes.show('data')",
	],
	'delete' => [
		'class' => "btn btn-danger bi-trash",
		'title' => "Delete this gymnast",
		'type' => "button",
		'onclick' => "magexes.show('delete')",
	],
	'export' => [
		'class' => "btn btn-primary bi bi-arrow-down-square",
		'title' => "Save the routines to your device",
		'type' => "button",
		'onclick' => "magexes.save()",
		'data-bs-toggle' => "modal"
	],
	'upload' => [
		'class' => "btn btn-primary bi bi-arrow-up-square",
		'title' => "Upload new routines from your device",
		'type' => "button",
		'onclick' => "magexes.show('upload')"
	],
	'clear' => [
		'class' => "btn btn-danger bi bi-trash",
		'title' => "Delete all app data",
		'type' => "button",
		'onclick' => "magexes.show('clear')"
	],
	'help' => [
		'class' => "btn btn-info bi-question-circle",
		'title' => "Button help",
		'type' => "button",
		'onclick' => "magexes.show('help')"
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
	exesets.log('switch to ' + rulesetname);
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

$attrs = ['class' => "bi bi-newspaper"];
$label = sprintf('<span %s></span>', stringify_attributes($attrs));
$attrs = [
	'title' => "view this rule set",
	'class' => "btn btn-primary-outline", 
	'id'=>"ruleset-link"
];
$link = anchor("#", $label, $attrs);
echo "<h6>Rule set {$link}</h6>";
?>
<p class="text-muted" id="help-ruleset"></p>
<p>Please tell Richard if you find a mistake in these pages.</p>

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
	<p class="text-muted">This will remove this app's data from your 
	browser (<span class="fst-italic">e.g. Edge, Chrome, FireFox, Safari</span>)
	.</p>
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
$keys = ['export', 'upload', 'clear'];
$tbody = [];
foreach($keys as $key) {
	$tbody[] = $buttons[$key];
}
$table = \App\Views\Htm\Table::load('small');
$table->autoHeading = false;
echo $table->generate($tbody);
?>
<p>All app data is stored within the 
browser (<span class="fst-italic">e.g. Edge, Chrome, FireFox, Safari</span>)
on your device (laptop, mobile, etc). These functions enable you to switch browser, use another device or create a backup.</p>  	
</div>

<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>

</div>
</div>
</div>

<div class="modal" id="dlg_upload" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title">Upload routines</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
<?php
$attrs = [];
$hidden = [];
echo form_open_multipart('', $attrs, $hidden);
?>
<p>Select the data file from your device. It will be named something like
<code><?php echo $filename; ?>.json</code>.
Uploading new a data file replaces the routines currently in the app.</p>
<fieldset class="mb-3 row">
	<div class="col-auto">
	<input class="form-control" type="file" name="upload">
	</div>
	
	<div class="col-auto">
	<button class="btn btn-primary" type="submit">upload</button>
	</div>
</fieldset>
<?php
echo form_close();
?> 	
</div>

<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
</div>

</div>
</div>
</div>

<div class="modal" id="dlg_import" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title">Use uploaded routines</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body"><?php 
$import = []; // used in JS magexes.import()
if($upload) {
	$list = []; 
	foreach($upload['exesets'] as $exeset) {
		$row = [
			'name' => $exeset->name ? 
				$exeset->name : 
				'<em class="text-body-secondary">[no name]</em>',
			'event' => $exeset->event ? 
				"({$exeset->event})" : 
				'(<em class="text-body-secondary">??</em>)'
		];
		$list[] = sprintf('<li>%s</li>', implode(' ', $row));
		$import[] = $exeset->export();
	}
	?>
	<p class="mb-0 fst-italic">The uploaded file contains the following entries:</p>
	<ul class="list-unstyled ms-1"><?php 
		echo implode("", $list);
	?></ul>
	<p>Replace existing data with routines in file
	<code><?php 
	$filename = $upload['file']->getClientname();
	echo basename($filename, '.json');
	?></code>?</p>
	<?php 
	# d($upload);
} 
?></div>

<div class="modal-footer">
<?php
$attrs = [
	'type' => "button",
	'class' => "btn btn-success",
	'title' => "Use this data (replace existing data)",
	'onclick' => "magexes.import()"
];
$label = '<span class="bi bi-upload"></span> use these routines';
printf('<button %s>%s</button>', stringify_attributes($attrs), $label);

echo $buttons['upload']['button'];
?>
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
</div>

</div>
</div>
</div>


<?php 
$attrs = ['id' => "magexes-save", 'class' => "d-none"];
$hidden = ['exesets' => ''];
echo form_open('ma2/export', $attrs, $hidden);
echo form_close();
?>

<script><?php
ob_start();
include __DIR__ . '/exesets.js';
?>

const magexes = {

init: function() {
	// get all modals on this page
	magexes.modals = {};
	let id = null, modalname = null;
	$('.modal').each(function(index) {
		modalname = this.id.substring(4);
		magexes.modals[modalname] = new bootstrap.Modal('#'+this.id);
	});
},

modals: null,
show: function(showname) {
	// prepare for modal display
	switch(showname) {
		case 'data':
		exesets.update();
		break;
		
		case 'delete':
		var entname = $('#editform [name=name]').val();
		$('#dlg_delete .entname').html(entname);
	}
	
	// hide any other modals 
	for(var modalname in magexes.modals) {
		var modal = magexes.modals[modalname];
		if(modalname==showname) {
			if(!modal._isShown) modal.show();
		}
		else {
			if(modal._isShown) modal.hide();
		}
	}
},
	
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
import: function() {
	var idata = <?php echo json_encode($import);?>;
	if(idata) {
		exesets.storage.set(idata);
		localStorage.setItem('mag-exesets-idx', 0);	
		window.location.assign('<?php echo base_url('ma2/routine');?>');
	}
}

}

$(function() {

magexes.init();
exesets.idx = localStorage.getItem('mag-exesets-idx') ?? 0;
var exeset = exesets.storage.load();
exesets.formdata.set(exeset);
<?php if($upload) { ?>
magexes.show('import');
<?php } ?>
});

<?php 
if(ENVIRONMENT=='development') { 
	echo ob_get_clean();
}
else { 
	$minifier = new MatthiasMullie\Minify\JS();
	$minifier->add(ob_get_clean());
	echo $minifier->minify();
}
?>
</script>

<?php $this->endSection(); 
