<?php $this->extend('default');

$this->section('content'); 
$table = new \CodeIgniter\View\Table();
$tbody = [];
foreach($users as $merge_user) {
	$source = sprintf('%s (%u)', $merge_user->name, $merge_user->id);
	$tbody[] = [
		'id' => $merge_user->id,
		'club' => sprintf('%s <a href="%s" title="view this user">%s</a>',
			$merge_user->deleted_at ? 
				'<span class="bi-x-circle text-danger"></span>' : 
				'<span class="bi-check-circle text-success"></span>',
			base_url("admin/users/view/{$merge_user->id}"),
			$merge_user->name),
		'role' => $merge_user->role ,
		'updated' => $merge_user->updated,
		'_' => sprintf('<button class="btn btn-danger btn-sm bi-layer-backward" type="button" name="merge" value="%u" data-bs-toggle="modal" data-bs-target="#mergeModal" data-bs-text="%s" data-bs-source="%u"></button>', $merge_user->id, $source, $merge_user->id)
	];
	
}
if($tbody) {
	$heading = array_fill(0, count($tbody[0]), '');
	$template = ['table_open' => '<table class="table compact">'];
	$table->setTemplate($template);
	$table->setHeading($heading);
	echo $table->generate($tbody);
}

$this->endSection(); 

$this->section('top'); ?>
<div class="toolbar"><?php 
	echo \App\Libraries\View::back_link("admin/users/view/{$user->id}");
?></div>
<p>Pull all information from the selected user into the current user, then delete the selected user.</p>
<p><strong>Current user: </strong> <?php printf('%s (role: %s)', $user->name, $user->role);?>.</p>
<?php if($user->deleted_at) { ?>
	<p class="alert-danger p-1"><span class="bi bi-x-circle"></span> User disabled</p>
<?php } 
$this->endSection(); 

$this->section('bottom'); ?>
<div class="modal fade" id="mergeModal" tabindex="-1" aria-hidden="true">
<div class="modal-dialog">
<?php 
	$action = '';
	$attr = [
		'class' => "modal-content"
	];
	$hidden = [
		'source' => ''
	];
	echo form_open($action, $attr, $hidden);
?>
<div class="modal-header">
	<h5 class="modal-title">Merge user data</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
	
	<p>You are about to merge user data. Are you sure?</p>
	<p><strong>Current user:</strong> <?php printf('%s (role: %s)', $user->name, $user->role);?>.</p>
	<p><strong>Pull all info from:</strong> <span class="source"></span>.<br>
	<span class="alert-danger">This user will be deleted</span>.</p>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
	<button type="submit" class="btn btn-danger">Merge</button>
</div>
<?php echo form_close(); ?>
</div>
</div>
<script>
var mergeModal = document.getElementById('mergeModal')
mergeModal.addEventListener('show.bs.modal', function (event) {
	// Button that triggered the modal
	var button = event.relatedTarget
	// Extract info from data-bs-* attributes
	mergeModal.querySelector('.source').textContent = button.getAttribute('data-bs-text');
	mergeModal.querySelector('[name=source]').value = button.getAttribute('data-bs-source');
})

</script>
<?php $this->endSection(); 
