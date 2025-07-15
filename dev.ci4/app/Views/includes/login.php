<?php
if(session('user_id')) return;
?>
<button type="button" class="btn btn-primary small py-1 mx-0 d-print-none m-2" data-bs-toggle="modal" data-bs-target="#dlglogin">login</button>

<div class="modal fade" id="dlglogin" tabindex="-1" aria-hidden="true">
<div class="modal-dialog modal-dialog-scrollable modal-lg">
<?php
$attrs = [
	'class' => "modal-content",
];
$hidden = [
	'login' => 'login',
];
echo form_open('', $attrs, $hidden);
?>

<div class="modal-header">
<h4 class="modal-title">Login</h4>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
<p class="form-floating">
	<input class="form-control" type="text" name="name" placeholder="user name" value="" required="" autofocus="">
	<label for="name" title="User name or club name" class="form-label">User name</label>
</p>
<p class="form-floating">
	<input class="form-control" type="password" name="password" placeholder="Password" value="" required="">
	<label class="form-label" for="password">Password</label>
</p>
</div>

<div class="modal-footer">
<button type="submit" class="btn btn-primary">Login</button>
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>

<?php echo form_close(); ?>
</div>
</div>

