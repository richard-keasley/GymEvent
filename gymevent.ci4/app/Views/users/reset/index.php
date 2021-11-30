<?php $this->extend('default');

$this->section('content');
echo form_open(base_url(uri_string()));
?>
<input type="hidden" name="reset" value="1">

<div class="mb-3">
	<label for="email" class="col-form-label">Enter the email address used for this account</label>
	<?php echo form_input("email", $email, 'class="form-control"', 'email');?>
</div>

<p><strong>Important:</strong> Contact Richard if you can no longer access messages for the email address used for this login.</p>
	
<div class="toolbar">
	<?php echo \App\Libraries\View::back_link('');?>
	<button class="btn btn-primary" type="submit">request reset</button>
</div>
</form>
<?php $this->endSection(); 
