<?php $this->extend('default');
 
$this->section('content');?>
<p>Please tell Richard Keasley if you spot any errors.</p>


<?php $this->endSection(); 

$this->section('top'); ?>
<div class="toolbar">
<?php echo \App\Libraries\View::back_link("mag");?>
</div>
<?php $this->endSection(); 
