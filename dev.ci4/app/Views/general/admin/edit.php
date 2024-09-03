<?php $this->extend('default');

$this->section('content'); 
$attr = [
	'id' => "editform"
];
echo form_open(current_url(), $attr); ?>
<p>While you <em title="death wish">can</em> edit this manually, it's designed to accept a pasted Excel spread-sheet.</p>
<textarea class="form-control" rows="15" name="value"><?php 
echo $textarea;
?></textarea>
<p>Updated: <?php 
echo $updated_at ? $updated_at->format('j M y') : '[never]';
?></p>
</form>

<section class="mt-3 table-responsive">
<?php 
if($value) {
	$table = \App\Views\Htm\Table::load('bordered');
	$table->setHeading(array_keys(current($value)));
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
