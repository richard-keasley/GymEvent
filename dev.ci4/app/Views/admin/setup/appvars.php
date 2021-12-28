<?php $this->extend('default');
$appvars = new \App\Models\Appvars();

$this->section('content'); 

$acc = new \App\Views\Htm\Accordion;
foreach($appvars->orderBy('id')->findAll() as $appvar) { 
	$acc->set_item($appvar->id, $appvar->htm());
}
echo $acc->htm();

$this->endSection(); 
