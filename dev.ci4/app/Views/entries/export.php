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

<div class="toolbar"><?php 
echo \App\Libraries\View::back_link("entries/view/{$event->id}");
echo getlink("/admin/entries/export/{$event->id}/csv", '<span class="bi-file-spreadsheet" title="Export as spreadsheet"></span>');
echo getlink("/admin/entries/export/{$event->id}/sql", '<span class="bi-file-code" title="Get SQL script"></span>');

?></div>

<?php $this->endSection(); 

