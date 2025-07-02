<?php $this->extend('kiosk');

$this->section('content'); ?>

<div style="width:34vw"><?php 
$img = [
    'src'   => 'app/scoreboard/qr-info.png',
    'alt'   => 'Event information',
    'style' => "max-width:100%;",
];
echo img($img);
?></div>

<div style="width:50vw;">
<p>View information on today's event!</p>
</div>

<?php $this->endSection();
