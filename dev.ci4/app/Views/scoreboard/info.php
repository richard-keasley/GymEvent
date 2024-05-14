<?php $this->extend('default');
helper('html');

$link = 'x/info';

$qr = [
    'src'   => 'app/scoreboard/qr-info.png',
    'alt'   => 'Event information',
    'style' => "width:12cm;max-width:100%;",
];

$anchor = [
	'title' => base_url($link)
];


$this->section('content'); ?>
<div class="text-center mb-2"><?php echo anchor($link, img($qr), $anchor);?></div>
<p class="display-3">View more information about the current event!</p>

<p class="display-6"><?php 
echo anchor($link, base_url($link), $anchor);

$attrs = [
	'title' => "View this in kiosk",
];
$text = '<span class="float-end bi bi-image d-print-none"></span>';
$href = 'scoreboard/info/kiosk';
echo anchor($href, $text, $attrs);

?></p>

<div style="margin-top:5rem;"><?php 
$attrs = [
	'src' => "app/profile/logo.png",
	'alt' => "GymEvent",
	'style' => "width:3cm; float:right;"
];
echo img($attrs);
?></div>

<?php $this->endSection();
