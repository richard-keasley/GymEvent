<?php $this->extend('default');

$this->section('content');

$attrs = [
	'id' => "editform"
];
echo form_open('', $attrs); ?>
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
	$trkey = 'description';
	foreach($value as $rowkey=>$row) {
		$value[$rowkey][$trkey] = new \App\Views\Htm\Pretty($row[$trkey]);
	}
		
	$table = \App\Views\Htm\Table::load('bordered');
	$table->setHeading(array_keys(current($value)));
	echo $table->generate($value);
}
?>
</section>
<?php $this->endSection(); 

$this->section('top'); ?>
<div class="toolbar sticky-top"> 
	<?php 
	echo \App\Libraries\View::back_link($back_link); 
	foreach($def_rules->exes as $exekey=>$exe) {
		if($exekey==$title) continue;
		$href = base_url("admin/general/edit/{$exekey}");
		$attrs = [
			'class' => "btn btn-outline-primary",
			'title' => $exe['name'],
		];
		echo anchor($href, $exekey, $attrs);	
	}
	?>
	<button form="editform" class="btn btn-primary" type="submit" name="save" value="1">save</button>
</div>
<?php $this->endSection(); 
