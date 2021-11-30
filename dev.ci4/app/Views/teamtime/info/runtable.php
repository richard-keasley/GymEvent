<?php $this->extend('default');
	
$this->section('content'); ?>
<p class="alert-warning"><strong>Warning:</strong> This page does not automatically update. Reload this page to get <em>current</em> information.</p>
<?php 
echo view('teamtime/displays/info/runtable');
$this->endSection(); 

$this->section('top'); ?>
<div class="toolbar sticky-top">
<?php echo \App\Libraries\View::back_link($back_link); ?>
</div>
<?php $this->endSection(); 
