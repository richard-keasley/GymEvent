<?php include __DIR__ . '/_head.php';
foreach(['name', 'email', 'password', 'password2', 'login'] as $key) {
	$postval[$key] = trim(strval(filter_input(INPUT_POST, $key)));
}
$show_reset = ($postval['name'] || $postval['password']) && \App\Libraries\Auth::check_path('reset');

// new users only for club returns
$current_url = new \CodeIgniter\HTTP\URI(current_url());
$segments = $current_url->getSegments();
$controller = $segments[0] ?? '' ;
$allow_new = $controller=='clubrets';

$attr = ['id' => "existing"];
$hidden = [
	'tabView' => '#existing',
	'hp-info' => ''
];
echo form_open($current_url, $attr, $hidden);

?>
<p>Your user name is your club name. 
<?php if($allow_new) { ?>
Please <strong>create an account</strong> if you have not used this service before.
<?php } ?></p>
 
<p class="form-floating">
	<input class="form-control" type="text" name="name" placeholder="user name" value="<?php echo $postval['name'];?>" required autofocus>
	<label for="name" title="User name or club name" class="form-label">User name</label>
</p>
<p class="form-floating">
	<input class="form-control" type="password" name="password" placeholder="Password" value="<?php echo $postval['password'];?>" required>
	<label class="form-label" for="password">Password</label>
</p>
<p>
	<button name="login" type="submit" value="login" class="btn btn-primary">Login</button>
	<?php if($allow_new) { ?>
	<button type="button" class="btn btn-outline-secondary" onclick="tabShow('#create')">Create an account</button>
	<?php } ?>
	<?php if($show_reset) { ?>
	<a title="forgot your password?" class="btn btn-outline-secondary" href="<?php echo site_url('reset');?>">Reset password</a>
	<?php } ?>
</p>
<?php 
// #existing
echo form_close();

if($show_reset) { ?>
<div class="bg-light p-2 border border-danger rounded">
<p>Please ensure you enter the username you supplied when creating your account. Repeated attempts to login with the wrong username password will result in an over-use injury.</p>
<p>Once you are logged in, you can alter your username and password using the links at the bottom of the screen.</p>
<p>Please contact Richard if you are having problems logging in.</p>
</div>
<?php } 

if($allow_new) { 
$attr = [
	'id' => "create",
	'autocomplete' => "off"
];
$hidden = [
	'tabView' => '#create',
	'hp-info' => ''
];
echo form_open(current_url(), $attr, $hidden);?>
<p class="form-floating">
	<input class="form-control" type="text" name="name" value="<?php echo $postval['name'];?>" id="newname" placeholder="" required autofocus>
	<label for="newname" title="Club name" class="form-label">Club name</label>
</p>
<p class="form-floating">
	<input class="form-control" type="password" name="password" value="<?php echo $postval['password'];?>" id="newpassword" placeholder="" autocomplete="new-password" required>
	<label class="form-label" for="newpassword">Password</label>
</p>
<p class="form-floating">
	<input class="form-control" type="password" name="password2" value="<?php echo $postval['password2'];?>" id="newpassword2" placeholder="" autocomplete="new-password">
	<label class="form-label" for="newpassword2">Repeat password</label>
</p>
<p class="form-floating">
	<input class="form-control" type="email" name="email" value="<?php echo $postval['email'];?>" id="newemail" placeholder="">
	<label class="form-label" for="newemail">Email</label> 
</p>
<p>
	<button name="login" value="new" type="submit" class="btn btn-primary">Create</button>
	<button type="button" class="btn btn-outline-secondary" onclick="tabShow('#existing')">Already got an account?</button>
</p>
<script>
tabShow('<?php echo filter_input(INPUT_POST, "tabView");?>');
function tabShow(tabView) {
	if(tabView!='#create') tabView = '#existing';
	var tabHide = tabView=='#existing' ? '#create' : '#existing' ;
	$(tabView).show();
	$(tabHide).hide();	
}
</script>
<?php 
// #create
echo form_close();
}

include __DIR__ . '/_foot.php';
