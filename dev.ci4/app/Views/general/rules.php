<?php $this->extend('default');
$table = new \CodeIgniter\View\Table();
$template = ['table_open' => '<table class="table table-bordered">'];
$table->setTemplate($template);

$this->section('content'); ?>
<p>Please tell Kim (Brighton) or Dave (Pegasus) if you see a problem on these pages.</p>
<?php 
foreach($rules as $name=>$rule_part) { 
	printf('<h3>%s</h3>', humanize($name));
	if(is_array($rule_part)) {
		$heading = [];
		foreach(array_keys(current($rule_part)) AS $val) {
			$heading[] = humanize($val);
		}
		$table->setHeading($heading);
		echo $table->generate($rule_part);
	}
	else {
		printf('<p class="alert-danger">%s</p>', $rule_part);
	}
}
$this->endSection(); 

$this->section('top');?>
<div class="toolbar sticky-top"> 
	<?php echo \App\Libraries\View::back_link($back_link); ?>
</div>
<?php $this->endSection(); 