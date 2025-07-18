<?php $this->extend('default');

$qr = [
    'src'   => 'app/scoreboard/qr-follow.png',
    'alt'   => 'Follow scores',
    'style' => "width:12cm;max-width:100%;",
];

$anchor = [
	'title' => base_url($link)
];


$this->section('content'); ?>
<div class="text-center mb-2"><?php echo anchor($link, img($qr), $anchor);?></div>
<p class="display-3">Follow scores during today's event!</p>
<p class="display-5">You need the entry number to see the scores.</p>

<p class="display-6"><?php 
echo anchor($link, base_url($link), $anchor);

$attrs = [
	'title' => "View this in kiosk",
];
$text = '<span class="float-end bi bi-image d-print-none"></span>';
$href = 'scoreboard/follow/kiosk';
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
