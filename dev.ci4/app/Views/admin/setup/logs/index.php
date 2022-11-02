<?php $this->extend('default');

$this->section('content'); ?>
<ul class="list-unstyled">
<?php 
foreach($tempfiles as $key=>$file) {
	$path = "setup/logs/view/{$key}";
	$label = $file->getBasename();
	$attr = [];
	printf('<li>%s</li>', anchor($path, $label, $attr));
}
?>
</ul>
<?php $this->endSection(); 

$this->section('top'); ?>
<div class="toolbar sticky-top"><?php
 	echo \App\Libraries\View::back_link("setup");
?></div>
<?php $this->endSection(); 