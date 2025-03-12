<?php $this->extend('default');

$this->section('content'); 
echo $html;
$this->endSection();

$this->section('bottom'); ?>

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
