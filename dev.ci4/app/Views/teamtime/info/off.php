<?php $this->extend('default');
	
$this->section('content');
echo $this->include('teamtime/displays/info/off');
$this->endSection(); 

$this->section('top'); ?>
<div class="toolbar sticky-top">
<?php echo \App\Libraries\View::back_link($back_link); ?>
</div>
<?php $this->endSection(); 
