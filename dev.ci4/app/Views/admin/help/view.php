<?php $this->extend('default');

$this->section('content');?>
<div class="item-image"><img src="<?php echo base_url('/app/profile/logo.png');?>"></div>
<?php 
if($include) include($include);
$this->endSection();

$this->section('top'); ?>
<div class="toolbar">
<?php echo \App\Libraries\View::back_link('admin/help'); ?>
</div>
<?php $this->endSection();


