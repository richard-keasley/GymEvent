<?php $this->extend('kiosk');

$this->section('content'); ?>

<div style="width:34vw"><?php 
$img = [
    'src'   => 'app/scoreboard/qr-follow.png',
    'alt'   => 'Follow scores',
    'style' => "max-width:100%;",
];
echo img($img);
?></div>

<div style="width:50vw;">
<p>Follow scores during today's event!</p>
<p>You need the entry number to see the scores.</p>
</div>

<?php $this->endSection();
