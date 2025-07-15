<?php $this->extend('default');

$this->section('content'); 
echo $html;
$this->endSection();

$this->section('bottom');
$buffer = getlink("setup/help/edit/{$html->id}", 'edit');
if($buffer) printf('<div class="toolbar">%s</div>', $buffer);	

$include = __DIR__ . "/_{$stub}.php";
if(is_file($include)) include $include;

echo $nav; ?>
<div style="max-width:25em"><img src="/app/profile/image.png"></div>
<?php
$this->endSection();

$this->section('top'); ?>
<div class="item-image"><img src="<?php echo base_url('/app/profile/logo.png');?>"></div>
<?php echo $nav;
$this->endSection();
