<?php $this->extend('default');

$this->section('content'); 
$attr = [
	'id' => "editform"
];
echo form_open(base_url(uri_string()), $attr);
?>
<textarea class="form-control" rows="20" name="teams"><?php 
$get_var = $tt_lib::get_var('teams');
foreach($get_var->value as $row) printf("%s\n", implode(' ', $row));
?></textarea>
</form>
<?php $this->endSection(); 

$this->section('top'); ?>
<div class="toolbar sticky-top"> 
	<?php echo \App\Libraries\View::back_link($back_link); ?>
	<button form="editform" class="btn btn-primary" type="submit" name="save" value="1">save</button>
</div>
<?php $this->endSection(); 
