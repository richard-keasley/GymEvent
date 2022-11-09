<?php $this->extend('default');
use \App\Libraries\Teamtime as tt_lib;

$this->section('content');  ?>
<p>This app displays the current state of a Team-gym event on many screens across a venue. Messaging, timers and music playback are included.</p>
<?php $this->endSection(); 

$this->section('sidebar'); ?>
<h5>Displays</h5>
<?php
$navbar = new \App\Views\Htm\Navbar(); 
$appvars = new \App\Models\Appvars();
$displays = $appvars->get_value('teamtime.displays');
if($displays) {
	$nav = [];
	foreach($displays as $key=>$display) {
		if($key) $nav[] = ["teamtime/display/{$key}", $display['title']];	
	}
	echo $navbar->htm($nav);
}
?>

<h5>Information</h5>
<?php 
$nav = [];
$viewpath = tt_lib::get_viewpath() . '*.php';
foreach(glob($viewpath) as $view) {
	$nav[] = sprintf('teamtime/info/%s', basename($view, '.php'));
}

echo $navbar->htm($nav);

$this->endSection();

$this->section('bottom'); ?>
<div class="toolbar"><?php
echo \App\Libraries\View::back_link('');
echo getlink('control/teamtime', 'TT control');
echo getlink('admin');
?></div>
<?php $this->endSection(); 
