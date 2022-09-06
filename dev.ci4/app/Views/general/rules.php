<?php $this->extend('default');
$table = \App\Views\Htm\Table::load('bordered');

$this->section('content'); ?>
<p>Please tell Kim (Brighton) or Dave (Pegasus) if you see a problem on these pages.</p>
<?php 
foreach($rules as $name=>$rule_part) { 
	printf('<h3>%s</h3>', humanize($name));
	if(is_array($rule_part)) { 
		?>
		<div class="table-responsive">
		<?php
		$heading = [];
		foreach(array_keys(current($rule_part)) AS $val) {
			$heading[] = humanize($val);
		}
		$table->setHeading($heading);
		echo $table->generate($rule_part);
		?>
		</div>
		<?php
	}
	else {
		printf('<p class="alert alert-danger">%s</p>', $rule_part);
	}
}
$this->endSection(); 

$this->section('top');?>
<div class="toolbar sticky-top"> 
	<?php echo \App\Libraries\View::back_link($back_link); ?>
</div>
<?php $this->endSection(); 
