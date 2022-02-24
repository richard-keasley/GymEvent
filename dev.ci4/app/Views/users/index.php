<?php $this->extend('default');

$this->section('content');
echo form_open(base_url(uri_string()));
?>
<div class="toolbar sticky-top">
<div class="mb-1">
	<button class="btn btn-secondary" type="submit" name="update" value="club0">Disable clubs</button>
	<button class="btn btn-secondary" type="submit" name="update" value="club1">Enable clubs</button>
	<a class="btn btn-secondary" href="<?php echo $base_url;?>/add">Add user</a>
	<a class="btn btn-secondary" href="<?php echo $base_url;?>/logins">Logins</a>
</div>
<div>
	<?php printf('<a class="btn btn-outline-secondary" href="%s">*</a>', $base_url); ?>
	<div class="btn-group">
	<?php foreach(\App\Libraries\Auth::roles as $role) {
		printf('<a class="btn btn-outline-secondary" href="%1$s?by=role&val=%2$s">%2$s</a>', $base_url, $role);
	} ?> 
	</div>
	
	<div class="btn-group">
	<?php foreach(['enabled', 'disabled'] as $status=>$label) {
		printf('<a class="btn btn-outline-secondary" href="%s?by=deleted&val=%u">%s</a>', $base_url, $status, $label);
	} ?>
	</div>
</div>	
</div>

<?php
echo \App\Views\Htm\Filter::htm();

$table = new \CodeIgniter\View\Table();
$template = ['table_open' => '<table class="table">'];
$table->setTemplate($template);

$thead = ['Name', 'role', ''];
$table->setHeading($thead);

$tbody = [];
$btn_enable = '<button type="submit" name="enable" title="enable" value="%1$u" class="btn btn-success bi-check-circle"></button><button type="submit" name="delete" title="delete this user and all related data" value="%1$u" class="btn btn-danger bi-trash"></button>';
$btn_disable = '<button type="submit" title="disable" name="disable" value="%1$u" class="btn bi-x-circle btn-danger"></button>';

foreach($users as $user) {
	if($user->self()) $btns = '[self]' ;
	else {
		$btns = $user->deleted_at ? 
			sprintf($btn_enable, $user->id) : 
			sprintf($btn_disable, $user->id);
	}		
	$tbody[] = [
		sprintf('%s <a href="%s/view/%u">%s</a>',
			$user->deleted_at ? 
				'<span class="bi-x-circle text-danger"></span>' : 
				'<span class="bi-check-circle text-success"></span>',
			$base_url, 
			$user->id, 
			$user->name
		),
		$user->role,
		$btns
	];
}
echo $table->generate($tbody);
echo form_close();
$this->endSection(); 
