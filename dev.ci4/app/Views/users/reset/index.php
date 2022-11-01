<?php $this->extend('default');

$this->section('content');
$attr = [];
$hidden = [
	'reset' => 1
];
echo form_open(current_url(), $attr, $hidden);
?>

<div class="mb-3">
	<label for="email" class="col-form-label">Enter the email address used for this account</label>
	<?php echo form_input("email", $email, 'class="form-control"', 'email');?>
</div>

<p><strong>Important:</strong> Contact Richard if you can no longer access messages for the email address used for this login.</p>
	
<div class="toolbar">
	<?php echo \App\Libraries\View::back_link('');?>
	<button class="btn btn-primary" type="submit">request reset</button>
</div>
<?php 
echo form_close();
$this->endSection(); 
