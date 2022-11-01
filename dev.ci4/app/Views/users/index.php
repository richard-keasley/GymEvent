<?php $this->extend('default');

$this->section('content');
echo form_open(current_url());
?>
<div class="sticky-top">
<div class="table-responsive toolbar">
<div>
	<a class="btn btn-secondary" href="<?php echo $base_url;?>/add">Add user</a>
	<a class="btn btn-secondary" href="<?php echo $base_url;?>/logins">Logins</a>

	<div class="btn-group"><?php
	printf('<a class="btn btn-outline-secondary" href="%s">*</a>', $base_url); 
	foreach(\App\Libraries\Auth::roles as $role) {
		printf('<a class="btn btn-outline-secondary" href="%1$s?by=role&val=%2$s">%2$s</a>', $base_url, $role);
	} 
	foreach(['enabled', 'disabled'] as $status=>$label) {
		printf('<a class="btn btn-outline-secondary" href="%s?by=deleted&val=%u">%s</a>', $base_url, $status, $label);
	}
	?></div>
</div>	
</div>
</div>

<?php
$filter = new \App\Views\Htm\Filter;
echo $filter->htm();

$table = \App\Views\Htm\Table::load('default');

$thead = ['Name', 'role', ''];
$table->setHeading($thead);

$tbody = [];
$btn_enable = '<button type="submit" name="enable" title="enable" value="%1$u" class="btn btn-success bi-check-circle"></button> <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modal_delete" data-bs-value="%1$u" data-bs-name="%2$s" title="Delete %2$s"><span class="bi bi-trash"></span></button>';
$btn_disable = '<button type="submit" title="disable" name="disable" value="%1$u" class="btn bi-x-circle btn-danger"></button>';

foreach($users as $user) {
	if($user->self()) $btns = '[self]' ;
	else {
		$username = htmlentities($user->name);
		$btns = $user->deleted_at ? 
			sprintf($btn_enable, $user->id, $username) : 
			sprintf($btn_disable, $user->id, $username);
	}		
	$tbody[] = [
		sprintf('%s <a href="%s/view/%u">%s</a>',
			$user->deleted_at ? 
				'<span title="user disabled" class="bi-x-circle text-danger"></span>' : 
				'<span title="user enabled" class="bi-check-circle text-success"></span>',
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

$this->section('bottom'); 
echo $this->include('includes/modal_delete');
?>
<script>
var modal = document.getElementById('modal_delete')
modal.addEventListener('show.bs.modal', function (event) {
	var button = event.relatedTarget;
	var value = button.getAttribute('data-bs-value');
	modal.querySelector('.dataname').textContent = button.getAttribute('data-bs-name');
	modal.querySelector('[name=item_id]').value = value;
});
</script>
<?php
echo form_close();
$this->endSection(); 
