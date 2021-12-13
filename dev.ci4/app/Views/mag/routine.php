<?php $this->extend('default');
 
$this->section('content');?>
<?php d($ruleset);?>
<?php d($routine);?>

<?php $this->endSection(); 

$this->section('top'); ?>
<div class="toolbar">
<?php echo \App\Libraries\View::back_link("mag");?>
</div>
<?php $this->endSection(); 
