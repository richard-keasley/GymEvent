<?php $this->extend('default');
use \App\Libraries\Teamtime as tt_lib;

$this->section('content'); 
$acc = new \App\Views\Htm\Accordion;
foreach(tt_lib::get_vars() as $appvar) {
	$htm = sprintf('<p>Updated %s</p>%s', $appvar->updated_at, $appvar->htm());
	$acc->set_item(substr($appvar->id, 9), $htm);
}
echo $acc->htm();
$this->endSection(); 

$this->section('bottom'); ?>
<div class="toolbar">
<?php echo \App\Libraries\View::back_link('control/teamtime'); ?>
</div>
<?php $this->endSection(); 
