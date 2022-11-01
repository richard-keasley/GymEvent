<?php $this->extend('default');

$this->section('content'); 

echo form_open(current_url()); ?>

<div class="toolbar">
	<?php echo \App\Libraries\View::back_link("admin");?>
	<button name="save" value="1" type="submit" class="btn btn-primary">save</button>
</div>

<h4>Active controllers / user roles</h4>
<div class="row">

<div class="col-auto">
<?php 
$tbody = [];
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

<div class="my-2 border rounded border-secondary p-1">
<label for="min_role" class="form-label">Minimum login</label>
<?php 
$options = [];
foreach(\App\Libraries\Auth::roles as $role) {
	$options[$role] = $role;
}
$input = [
	'class' => 'form-select',
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

<div class="col-auto">
<p>You are viewing device 
<code><?php echo $device;?></code> -
<code><?php echo base_url();?></code>.</p>

<nav class="nav flex-column">
<?php 
echo getlink('admin/help/stub?view=setup', 'Permissions');
echo getlink('setup/scoreboard', 'Scoreboard');
echo getlink('setup/php_info', 'PHP info');
echo getlink('setup/appvars', 'App variables');
echo getlink('setup/dev', 'Development notes');
if($device=='development') echo getlink('setup/update', 'Update the App');
echo getlink('setup/install', 'Installation notes');
echo getlink('setup/logs', 'Error logs');
?>
</nav>

</div>

<div class="col-auto">
<?php echo $this->include('includes/version');?>
</div>

</div>

<?php 
echo form_close();
$this->endSection(); 
