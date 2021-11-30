<?php $this->extend('default');

$this->section('content');  ?>
<p>This app displays the current state of a Team-gym event on many screens across a venue. Messaging, timers and music playback are included.</p>
<?php $this->endSection(); 

$this->section('sidebar'); ?>
<h5>Displays</h5>
<?php 
$appvars = new \App\Models\Appvars();
$displays = $appvars->get_value('teamtime.displays');
if($displays) {
	$navbar = ['nav'=>[]];
	foreach($displays as $key=>$display) {
		if($key) $navbar['nav'][] = ["teamtime/display/{$key}", $display['title']];	
	}
	echo view('includes/navbar', $navbar);
}
?>

<h5>Information</h5>
<?php 
$navbar = ['nav' => [
	'teamtime/info/programme',
	'teamtime/info/teams',
	'teamtime/info/runtable',
	'teamtime/info/images',
	'teamtime/info/music'
]];
echo view('includes/navbar', $navbar);
?>
<?php $this->endSection();

$this->section('bottom'); ?>
<div class="toolbar"><?php
echo \App\Libraries\View::back_link('');
echo getlink('admin/teamtime', 'TT admin');
echo getlink('admin');
?></div>
<?php $this->endSection(); 
