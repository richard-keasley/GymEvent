<?php $this->extend('default');
$tt_lib = new \App\Libraries\Teamtime();

$this->section('content'); 

$acc = new \App\Views\Htm\Accordion;
foreach($tt_lib::get_vars() as $appvar) {
	$htm = "<p>Updated {$appvar->updated_at}</p>";
	$htm .= $appvar->htm();
	$acc->set_item(substr($appvar->id, 9), $htm);
}
echo $acc->htm();
$this->endSection(); 

$this->section('bottom'); ?>
<div class="toolbar">
<?php echo \App\Libraries\View::back_link('admin/teamtime'); ?>
</div>
<?php $this->endSection(); 
