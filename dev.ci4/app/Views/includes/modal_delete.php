<?php
$action = $modal_delete['action'] ?? ''; # current_url(true);
$id = $modal_delete['id'] ?? 'modal_delete';
$cmd = $modal_delete['cmd'] ?? 'del_item';
$item_id = $modal_delete['item_id'] ?? 0;
$title = $modal_delete['title'] ?? 'Delete item';
$description = $modal_delete['description'] ?? '<p>Delete this item?</p>';
$icon = $modal_delete['icon'] ?? 'trash';
?>
<div id="<?php echo $id;?>" class="modal" tabindex="-1">
<div class="modal-dialog">
<?php
$attr = [
	'class' => "modal-content"
];
$hidden = [
	'cmd' => $cmd,
	'item_id' => (string) $item_id
];
echo form_open($action, $attr, $hidden);
?>
<div class="modal-header">
	<h5 class="modal-title"><?php echo $title;?></h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
	<?php echo $description;?>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary bi-x-circle-fill" data-bs-dismiss="modal" title="cancel"></button>
	<button type="submit" class="btn btn-danger bi-<?php echo $icon;?>" title="proceed"></button>
</div>
<?php echo form_close();?>
</div>
</div>
