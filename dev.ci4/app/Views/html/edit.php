<?php $this->extend('default');

$this->section('content');

$attrs = ['id' => "editform"];
$hidden = ['save' => '1'];
echo form_open('', $attrs, $hidden); 

?>
<div class="row my-1">
	<div class="col-auto text-end"><label class="form-label" style="width:4em;">
		Path
	</label></div>
	<div class="col-auto"><?php 
	$input = [
		'class' => 'form-control',
		'name' => "path",
		'value' => $html->path,
		'style' => "min-width:10em"
	];
	echo form_input($input); 
	?></div>
</div>
<div class="row my-1">
	<div class="col-auto text-end"><label class="form-label" style="width:4em;">
		Heading
	</label></div>
	<div class="col-auto"><?php 
	$input = [
		'class' => 'form-control',
		'name' => "heading",
		'value' => $html->heading,
		'style' => "min-width:10em"
	];
	echo form_input($input); 
	?></div>
</div>

<div><?php
$attrs = [
	'name' => 'value',
	'value' => $html->value
];
echo new \App\Views\Htm\Editor($attrs);
?></div>

<p><strong>Updated:</strong> <?php 
echo (new \datetime($html->updated))->format('d M Y');
?></p>
<?php 
echo form_close();
$this->endSection();

$this->section('top'); ?>
<div class="toolbar">
<?php echo \App\Libraries\View::back_link("setup/help/view/{$html->id}"); ?>
<button class="btn btn-primary" type="submit" name="cmd" value="save" form="editform">save</button>
<button type="button" title="Delete this entry" class="btn btn-danger bi-trash" data-bs-toggle="modal" data-bs-target="#modal_delete"></button>
</div>
<?php $this->endSection();

$this->section('bottom');
echo $this->include('includes/modal_delete');
$this->endSection();


