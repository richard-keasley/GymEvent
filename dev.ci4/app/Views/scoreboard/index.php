<?php $this->extend('default');

$this->section('content'); ?>
<div class="item-image"><img src="/app/scoreboard/screen.png" alt="screen"></div>

<?php
$appvars = new \App\Models\Appvars();
echo $appvars->get_value('scoreboard.home');
?>

<div class="toolbar"><?php 
echo \App\Libraries\View::back_link("/");
echo getlink('setup/scoreboard', 'admin');

$appvars = new \App\Models\Appvars();
$links = $appvars->get_value('home.links');

$keys = ['follow', 'info'];
foreach($keys as $key) {
	$link = $links[$key] ?? null ;
	if($link) echo getlink("scoreboard/{$key}");
}
?></div>

<?php $this->endSection();
	