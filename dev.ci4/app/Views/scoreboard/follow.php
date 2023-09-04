<?php $this->extend('default');
helper('html');
$link = 'https://sb.gymevent.uk/kiosk#/entry-info';

$img = [
    'src'   => '/app/scoreboard/follow.svg',
    'alt'   => 'Follow scores',
    'style' => "width:12cm;max-width:100%;",
];

$anchor = [
	'title' => $link,
	'class' => "d-block"
];


$this->section('content'); ?>
<div class="text-center"><?php echo anchor($link, img($img), $anchor);?></div>
<p class="display-3">Scan to follow the scores for today's event!</p>
<p class="display-5">You will need the entry number of the gymnast you are following.</p>
<p class="display-6"><?php echo anchor($link, $link, $anchor);?></p>
<img src="/app/profile/logo.png" alt="GymEvent" style="width:3cm; position:fixed;right:0; border:0; bottom:0;">
<?php $this->endSection();
