<?php $this->extend('default');
$table = new \CodeIgniter\View\Table();
$template = ['table_open' => '<table class="table table-sm">'];
$table->setTemplate($template);

$this->section('content'); 

echo form_open(base_url(uri_string())); ?>
<h4>Active controllers</h4>
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
</div>

<div class="col-auto">
<p>The following controllers can not be disabled: 
	<code><?php echo implode('</code>, <code>', $locked_controllers); ?></code>.
</p>

<nav class="flex-column">

<div class="nav-link text-primary" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#modalHelp" data-stub="setup">Permissions</div>
<?php 
echo getlink('setup/scoreboard', 'Scoreboard');
echo getlink('setup/php_info', 'PHP info');
echo getlink('setup/appvars', 'App variables');
echo getlink('setup/dev', 'Development notes');
echo getlink('setup/install', 'Installation notes');
?></nav>

</div>

<div class="col-auto">
<?php echo view('includes/version');?>
</div>

</div>

<div class="toolbar">
	<?php echo \App\Libraries\View::back_link("admin");?>
	<button name="save" value="1" type="submit" class="btn btn-primary">save</button>
</div>

</form>

<?php $this->endSection(); 

