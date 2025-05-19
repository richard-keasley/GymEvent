<fieldset><legend>User details</legend>
<div class="row mb-3">
	<label for="user_name" class="col-sm-2 col-form-label">Club name</label>
	<div class="col-sm-10">
	<?php echo form_input("user_name", $user->name, 'class="form-control"');?>
	<p class="d-none alert alert-warning m-0"><small>Careful if you change this; it is your username when you login. You may need to update your browser's password manager as well.</small></p>
	</div>
</div>
<div class="row mb-3">
	<label for="user_email" class="col-sm-2 col-form-label">Email</label>
	<div class="col-sm-10"><?php echo form_input("user_email", $user->email, 'class="form-control"', 'email');?></div>
</div>
</fieldset>

<fieldset><legend>Contact details</legend>
<div class="row mb-3">
	<label for="name" class="col-sm-2 col-form-label">name</label>
	<div class="col-sm-10"><?php echo form_input("name", $clubret->name, 'class="form-control"');?></div>
</div>
<div class="row mb-3">
	<label for="address" class="col-sm-2 col-form-label">address</label>
	<div class="col-sm-10"><?php echo form_textarea(['name'=>"address", 'value'=>$clubret->address, 'rows'=>"4", 'class'=>"form-control"]);?></div>
</div>
<div class="row mb-3">
	<label for="phone" class="col-sm-2 col-form-label">phone</label>
	<div class="col-sm-10"><?php 
	$input = [
		'name' => "phone",
		'value' => $clubret->phone,
		'class' => "form-control",
		'type' => 'tel'
	];
	echo form_input($input);
	?></div>
</div>
<div class="row mb-3">
	<label for="other" class="col-sm-2 col-form-label">other</label>
	<div class="col-sm-10"><?php echo form_textarea(['name'=>"other", 'value'=>$clubret->other, 'class'=>"form-control", 'rows'=>5]);?></div>
</div>
</fieldset>