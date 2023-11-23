<?php $this->extend('default');

$this->section('content'); 
echo form_open(current_url()); ?>

<div class="toolbar sticky-top">
	<?php echo \App\Libraries\View::back_link("admin");?>
	<button name="save" value="1" type="submit" class="btn btn-primary">save</button>
</div>

<h4>Active controllers / user roles</h4>
<div class="row">

<div class="col-auto">
<?php 
foreach($controllers as $controller=>$enabled) { ?>
	<div class="form-check form-switch">
	<?php
	$input = [
		'class' => 'form-check-input',
		'value' => 1,
		'title' => 'enabled',
		'id' => "chk_{$controller}",
		'name' => "chk_{$controller}"
	];
	if($enabled) $input['checked'] = 'checked'; 
	if(in_array($controller, $locked_controllers)) $input['disabled'] = 'disabled';
	echo form_checkbox($input);
	printf('<label class="form-check-label" for="%s">%s</label>', $input['id'], $controller);
	?>
	</div>
<?php } ?>
</div>

<div class="col-auto">
<p>You are viewing device:<br> 
<code><?php echo $device;?><br>
<?php echo base_url();?><br>
<?php echo filter_input(INPUT_SERVER, 'SERVER_ADDR');?></code></p>

<nav class="nav flex-column">
<?php 
echo getlink('admin/help/stub?view=setup', 'Permissions');
echo getlink('setup/scoreboard', 'Scoreboard');
echo getlink('setup/links', 'Shortcut links');
echo getlink('setup/php_info', 'PHP info');
echo getlink('setup/appvars', 'App variables');
echo getlink('setup/dev', 'Development notes');
if($device=='development') echo getlink('setup/update', 'Update the App');
echo getlink('setup/install', 'Installation notes');
echo getlink('setup/logs', 'Error logs');
echo getlink('setup/session', 'Session files');
?>
</nav>

</div>

<div class="col-auto">
<div class="card bg-light">
<div class="card-header">Login checks</div>
<div class="card-body">
<?php
echo new \App\Views\Htm\Vartable(\App\Models\Logins::$config);
?>

<div class="row">
<div class="col-auto">
<label for="min_role" class="form-label"><strong>Minimum login</strong></label>
</div>
<div class="col-auto">
<?php 
$options = [];
foreach(\App\Libraries\Auth::roles as $role) {
	$options[$role] = $role;
}
$input = [
	'class' => 'form-select bg-light',
	'selected' => \App\Libraries\Auth::$min_role,
	'title' => 'Minimum login role',
	'name' => "min_role",
	'id' => "min_role",
	'options' => $options
];
echo form_dropdown($input);
?>
</div>
</div>
</div>
	
</div>
</div>

<div class="col-auto">
<?php echo $this->include('includes/version');?>
</div>

</div>

<?php 
echo form_close();
$this->endSection(); 
