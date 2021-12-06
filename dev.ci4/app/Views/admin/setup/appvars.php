<?php $this->extend('default');
$appvars = new \App\Models\Appvars();

$this->section('content'); 

$acc = new \App\Libraries\Ui\Accordion;
foreach($appvars->orderBy('id')->findAll() as $appvar) { 
	$acc->set_item($appvar->id, view('includes/appvar', ['appvar'=>$appvar]));
}
echo $acc->htm();

$this->endSection(); 
