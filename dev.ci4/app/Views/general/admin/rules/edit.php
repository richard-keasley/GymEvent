<?php $this->extend('default');
$table = new \CodeIgniter\View\Table();

$this->section('content'); 
$attr = [
	'id' => "editform"
];
echo form_open(base_url(uri_string()), $attr); ?>
<p>While you <em title="death wish">can</em> edit this manually, it's designed to accept a pasted Excel spread-sheet.</p>
<textarea class="form-control" rows="15" name="value"><?php 
echo $textarea;
?></textarea>
<p>Updated: <?php echo $updated_at->format('j M y');?></p>
</form>

<section class="mt-3 table-responsive">
<?php 
if($value) {
	$table->setTemplate(\App\Libraries\Table::templates['primary']);
	$table->setHeading(array_keys(current($value)));
	foreach($value as $row_key=>$row) {
		foreach($row as $key=>$val) {
			if(in_array($key, \App\Libraries\General\Skills::attributes)) {
				$value[$row_key][$key] = $val ? '<span class="bi bi-check-circle-fill text-success"></span>' : '&nbsp;';
			}
		}
	}
	echo $table->generate($value);
}
?>
</section>
<?php $this->endSection(); 

$this->section('top'); ?>
<div class="toolbar sticky-top"> 
	<?php echo \App\Libraries\View::back_link($back_link); ?>
	<button form="editform" class="btn btn-primary" type="submit" name="save" value="1">save</button>
</div>
<?php $this->endSection(); 

 
