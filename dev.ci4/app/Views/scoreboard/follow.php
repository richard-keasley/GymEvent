<?php $this->extend('default');
helper('html');

$link = 'follow';

$img = [
    'src'   => '/app/scoreboard/qr-follow.png',
    'alt'   => 'Follow scores',
    'style' => "width:12cm;max-width:100%;",
];

$anchor = [
	'title' => base_url($link),
	'class' => "d-block"
];


$this->section('content'); ?>
<div class="text-center"><?php echo anchor($link, img($img), $anchor);?></div>
<p class="display-3">Scan to follow the scores for today's event!</p>
<p class="display-5">You will need the entry number of the gymnast you are following.</p>
<p class="display-6"><?php echo anchor($link, base_url($link), $anchor);?></p>
<img src="/app/profile/logo.png" alt="GymEvent" style="width:3cm; position:fixed;right:0; border:0; bottom:0;">
<?php $this->endSection();
