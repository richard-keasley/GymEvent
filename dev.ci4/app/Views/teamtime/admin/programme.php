<?php $this->extend('default');
use \App\Libraries\Teamtime as tt_lib;

$this->section('content');
echo tt_lib::view_html('programme');
$this->endSection(); 

$this->section('top'); ?>
<div class="toolbar sticky-top"> 
	<?php echo \App\Libraries\View::back_link('control/teamtime'); ?>
	<button class="btn btn-primary bi bi-pencil" title="edit programme" type="button" data-bs-toggle="modal" data-bs-target="#pageModal"></button>
</div>
<?php $this->endSection(); 

$this->section('bottom'); ?>
<div class="modal fade" id="pageModal" tabindex="-1" aria-hidden="true">
<div class="modal-dialog">
<?php 
$attr = [
	'class' =>"modal-content"
];
echo form_open(current_url(), $attr); ?>
<div class="modal-header">
	<h5 class="modal-title" id="exampleModalLabel">Edit programme</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
	<p>Multiple words in section headers (e.g. "Orientation 1") must be joined with an underscore (e.g. <code>orientation_1</code>).</p>
	<?php 
	$value = [];
	foreach(tt_lib::get_value('progtable') as $row) {
		array_shift($row); // remove run mode
		$value[] = implode("\t\t\t", $row);
	}

	$input = [
		'class' => "form-control",
		'rows' => "20",
		'name' => "progtable",
		'value' => implode("\n", $value)
	];
	echo form_textarea($input);
	?>
</div>
<div class="modal-footer">
	<button class="btn btn-primary" type="submit" name="save" value="1">Save</button>
	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
<?php echo form_close(); ?>
</div>
</div>
<?php $this->endSection(); 
