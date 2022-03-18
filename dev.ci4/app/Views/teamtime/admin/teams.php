<?php $this->extend('default');

$this->section('content'); 
echo \App\Libraries\Teamtime::view_html('teams');
$this->endSection(); 

$this->section('top'); ?>
<div class="toolbar sticky-top"> 
	<?php echo \App\Libraries\View::back_link('control/teamtime'); ?>
	<button class="btn btn-primary bi bi-pencil" title="view programme" type="button" data-bs-toggle="modal" data-bs-target="#pageModal"></button>
</div>
<?php $this->endSection(); 

$this->section('bottom'); ?>
<div class="modal fade" id="pageModal" tabindex="-1" aria-hidden="true">
<div class="modal-dialog">
<?php 
$attr = [
	'class' =>"modal-content"
];
echo form_open(base_url(uri_string()), $attr); ?>
<div class="modal-header">
	<h5 class="modal-title" id="exampleModalLabel">Edit teams</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
	<?php 
	$value = [];
	$get_var = $tt_lib::get_var('teams');
	foreach($get_var->value as $row) {
		$value[] = implode("\t", $row);
	}

	$input = [
		'class' => "form-control",
		'rows' => "20",
		'name' => "teams",
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
