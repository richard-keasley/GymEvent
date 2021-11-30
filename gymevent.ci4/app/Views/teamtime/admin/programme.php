<?php $this->extend('default');

$this->section('content');
$attr = [
	'id' => "editform"
];
echo form_open(base_url(uri_string()), $attr); ?>
<p>Multiple words in section headers (e.g. "Orientation 1") must be joined with an underscore (e.g. <code>orientation_1</code>).</p>
<textarea class="form-control" rows="20" name="progtable"><?php 
$get_var = $tt_lib::get_var('progtable');
foreach($get_var->value as $row) {
	array_shift($row); // remove run mode
	printf("%s\n", implode("\t\t", $row));
}
?></textarea>
</form>
<?php $this->endSection(); 

$this->section('top'); ?>
<div class="toolbar sticky-top"> 
	<?php echo \App\Libraries\View::back_link($back_link); ?>
	<button form="editform" class="btn btn-primary" type="submit" name="save" value="1">save</button>
</div>
<?php $this->endSection(); 

