<?php
if(empty($users)) $users = [];
if(empty($title)) $title= "user";
if(empty($user_id)) $user_id= 0;
if(empty($description)) $description = 'Select user.';
if(empty($cmd)) $cmd = 'modalUser';

$hidden = [
	'cmd' => $cmd
];
$attr = [
	'id' => "modalUser",
	'class' => "modal fade",
	'tabindex' => "-1",
	'aria-hidden' => "true"
];
echo form_open(base_url(uri_string()), $attr, $hidden);
?>
<div class="modal-dialog modal-dialog-scrollable">
<div class="modal-content">
<div class="modal-header">
	<h5 class="modal-title"><?php echo $title;?></h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
<p><?php echo $description;?></p>
<?php
$filter = new \App\Views\Htm\Filter('#modalUser .modal-body .nav-item');
echo $filter->htm();
?>
<ul class="nav flex-column">
<?php foreach($users as $user) { ?>
	<li class="nav-item"><?php 
	$selected = $user_id==$user->id ? 'btn-outline-success' : '' ;
	printf('<button class="btn %s" type="submit" name="user_id" value="%u">%s</button>', $selected, $user->id, $user->name);
	?></li>
<?php } ?>
</ul>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
</div>
</div>
<?php
echo form_close();
