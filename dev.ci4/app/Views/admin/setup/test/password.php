<?php $this->extend('default');

$this->section('content'); ?>

<?php echo form_open(); ?>
<div class="d-flex gap-1 p-1"><label>Password</label> <?php
$value = trim($postvars['pwdcheck'] ?? '');
$attrs = [
	'name' => "pwdcheck",
	'value' => $value,
	'style' => "max-width:15em",
	'class' => "form-control"
];
echo form_input($attrs);
?>
<input type="submit" class="btn btn-primary" value="TEST">
</div>

<div class="p-1 alert alert-info">complexity: <?php
echo \App\Libraries\Auth::complexity($value);
?></div>

<?php echo form_close();

$this->endSection();
