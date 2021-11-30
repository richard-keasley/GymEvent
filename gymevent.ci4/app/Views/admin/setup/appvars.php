<?php $this->extend('default');
$appvars = new \App\Models\Appvars();
$acc = new \App\Libraries\Ui\Accordion;

$this->section('content'); 

echo $acc->start('accAppvar');
foreach($appvars->orderBy('id')->findAll() as $appvar) { 
	echo $acc->item_start($appvar->id);
	echo view('includes/appvar', ['appvar'=>$appvar]);
}
echo $acc->end();

$this->endSection(); 
