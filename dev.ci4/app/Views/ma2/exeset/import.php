<?php $this->extend('default');
$import = []; 

$this->section('content'); 

$attrs = [
	'class' => "btn btn-outline-secondary",
	'title' => "Cancel"
];
$label = '<span class="bi bi-x-circle"></span>';
$cancel_button = anchor("ma2/routine", $label, $attrs);
 
if($file && $exesets) { 

$list = [];
foreach($exesets as $exeset) {
	$row = [
		'name' => $exeset->name ? 
			$exeset->name : 
			'<em class="text-body-secondary">[no name]</em>',
		'event' => $exeset->event ? 
			$exeset->event : 
			'<em class="text-body-secondary">??</em>'
	];
	$list[] = implode(', ', $row);
	$import[] = $exeset->export();
}

$attrs = [];
$hidden = [
	'exesets' => json_encode($import),
	'import' => "1"
];
echo form_open('', $attrs, $hidden);
?>
<h5>Uploaded file</h5>
<p class="mb-0">The uploaded file contains the following entries:</p>

<ul class="list-unstyled ms-1"><?php 
foreach($list as $li) echo "<li>{$li}</li>";
?></ul>

<p>Replace existing data with routines held in file <code><?php echo $file->getClientname();?></code>?</p>

<div class="toolbar"><?php
echo $cancel_button;

$attrs = [
	'type' => "button",
	'class' => "btn btn-primary",
	'title' => "Use this data (replace existing data)",
	'onclick' => "import_data()"
];
$label = '<span class="bi bi-upload"></span>';
printf('<button %s>%s</button>', stringify_attributes($attrs), $label);
?></div>

<?php 
# d($file, $exesets);
echo form_close();
}

$attrs = [];
$hidden = [];
echo form_open_multipart('', $attrs, $hidden);
?>
<fieldset><legend>Upload new routine set</legend>

<div class="mb-3 row">
	<div class="col-auto"><?php 
	echo $cancel_button;
	?></div>
	
	<div class="col-auto">
	<input class="form-control" type="file" name="import">
	</div>
	
	<div class="col-auto">
	<button class="btn btn-primary" type="submit">upload</button>
	</div>
</div>
</fieldset>
<?php
echo form_close();

$this->endSection(); 

$this->section('bottom'); ?>
<script><?php
ob_start();
include __DIR__ . '/exesets.js';
?>
function import_data() {
	exesets.storage.set(<?php echo json_encode($import);?>);
	localStorage.setItem('mag-exesets-idx', 0);	
	window.location.assign('<?php echo base_url('ma2/routine');?>');
}
<?php 
echo ob_get_clean();
/*

$minifier = new MatthiasMullie\Minify\JS();
$minifier->add(ob_get_clean());
echo $minifier->minify();
*/
?>
</script>
<?php $this->endSection(); 
