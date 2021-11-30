<?php $this->extend('default');
$tt_lib = new \App\Libraries\Teamtime();
$acc = new \App\Libraries\Ui\Accordion;

$this->section('content'); 
echo $acc->start('accAppvar');
foreach($tt_lib::get_vars() as $appvar) {
	$varname = substr($appvar->id, 9);
	echo $acc->item_start($varname);
	echo view('includes/appvar', ['appvar'=>$appvar]);
}
echo $acc->end();
$this->endSection(); 

$this->section('bottom'); ?>
<div class="toolbar">
<?php echo \App\Libraries\View::back_link('admin/teamtime'); ?>
</div>
<?php $this->endSection(); 
