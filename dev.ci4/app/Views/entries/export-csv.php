<?php $this->extend('default');

$this->section('content');

$table = new \CodeIgniter\View\Table();

if($export) {
	$template = ['table_open' => '<table class="table table-border"	>'];
	$table->setTemplate($template);
	$table->setHeading(array_keys($export[0]));
	echo $table->generate($export);
}

$this->endSection(); 

$this->section('top'); ?>

<div class="toolbar">
<?php echo \App\Libraries\View::back_link("entries/view/{$event->id}");?>

</div>

<?php $this->endSection(); 

