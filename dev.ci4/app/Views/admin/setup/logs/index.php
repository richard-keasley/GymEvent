<?php $this->extend('default');

$this->section('content'); ?>
<ul>
<?php 
foreach($logfiles as $key=>$file) {
	$path = "setup/logs/view/{$key}";
	$label = $file->getBasename();
	$attr = [];
	printf('<li>%s</li>', anchor(base_url($path), $label, $attr));

}
?>
</ul>

<?php $this->endSection(); 