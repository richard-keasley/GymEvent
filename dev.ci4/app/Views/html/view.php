<?php $this->extend('default');

$this->section('content');?>
<h3><?php echo $html->heading;?></h3>
<p class="border p-1"><strong>Path:</strong> <?php echo $html->path;?></p>
<div><?php
echo $html;
?></div>

<?php 
$this->endSection();

$this->section('top'); ?>
<div class="toolbar">
<?php echo \App\Libraries\View::back_link('setup/help'); ?>
<a class="btn btn-outline-primary" href="<?php echo site_url("setup/help/edit/{$html->id}");?>"><span class="bi bi-pencil"></span></a>
</div>
<?php $this->endSection();
