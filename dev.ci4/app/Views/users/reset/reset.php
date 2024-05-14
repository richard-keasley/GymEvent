<?php $this->extend('default');

$this->section('content');
$attrs = ['autocomplete' => "off"];
$hidden = ['reset' => "1"];
echo form_open('reset/reset', $attrs, $hidden);
?>
<p>Enter the 'reset key' (emailed to you) and your new password. The reset key is valid for 20 minutes.</p>
<div class="mb-3">
	<label class="col-form-label">Reset key</label>
	<input name="key" class="form-control" value="<?php echo $key;?>">
</div>
<div class="mb-3">
	<label title="Enter your new password" class="col-form-label">New password</label>
	<input name="password" type="password" autocomplete="new-password" class="form-control">
</div>
<div class="toolbar">
	<button class="btn btn-primary" type="submit">reset</button>
</div>
</form>
<?php 

# d($user);
$this->endSection(); 
