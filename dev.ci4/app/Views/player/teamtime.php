<?php $this->extend('default');
use \App\Libraries\Teamtime as tt_lib;

$this->section('content');

$table = \App\Views\Htm\Table::load('responsive');
$progtable = tt_lib::get_value('progtable');
$teams = tt_lib::get_value('teams');
$rundata = tt_lib::get_rundata();
# d($rundata);

$exes = [];
if($progtable) {
	$exes = array_shift($progtable);
	$mode = array_shift($exes); // discard mode
	$thead = [''];
	foreach($exes as $exe) {
		$thead[] = \App\Views\Htm\Table::centre($exe);
	}
	$table->setHeading($thead);
}
else { ?>
<p class="alert alert-danger">Programme appears to be empty</p>
<?php } 

if(!$teams) { ?>
<p class="alert alert-danger">Teams are not set-up</p>
<?php }

echo "<h2>{$h2}</h2>";

$track = new \App\Libraries\Track();
$track->event_id = $event_id;
# printf('<p>%s</p>', $track->urlpath());

$tbody = [];
$empty = \App\Views\Htm\Table::centre('<i class="text-danger bi-x" title="this track does not appear in the programme"></i>');

foreach($teams as $team) {
	$tr = ['team' => implode('. ', $team)];
	$entry_num = $team[0];
	$track->entry_num = $entry_num;
	foreach($exes as $exe) {
		$track->exe = $exe;
		$tr[$exe] = empty($rundata[$entry_num][$exe]) ? 
			$empty : 
			$track->playbtn(['player']) ;
	}
	$tbody[] = $tr;
}
echo $table->generate($tbody);
$this->endSection(); 

$this->section('top');
echo $this->include('player/local');
?>
<div class="sticky-top pb-1"><?php 
echo $this->include('Htm/Playtrack');
?></div>
<script>
$(function() {

$('button[name=trk]').click(function() {
	var track_url = playtrack.button(this);
});
	
});
</script>
<?php $this->endSection();

$this->section('bottom'); ?>
<div class="toolbar"><?php
$links = [];

if($action=='save') {
	$links[] = [
		'label' => '<i class="bi bi-globe"></i>',
		'attrs' => [
			'title' => "View media player online",
			'class' => "btn btn-outline-secondary"
		],
		'href' => "control/teamtime/player"
	];
}

if($action=='view') {
	echo \App\Libraries\View::back_link('control/teamtime');
	$links[] = [
		'label' => '<i class="bi bi-download"></i>',
		'attrs' => [
			'title' => "Save media player to local device",
			'class' => "btn btn-primary"
		],
		'href' => current_url() . "/save"
	];
	
}
$links[] = [
	'label' => '<span class="bi bi-broadcast"></span>',
	'attrs' => [
		'title' => "Start player receiver",
		'class' => "btn btn-outline-secondary",
		'target' => "ssereceiver",
	],
	'href' => "control/player/receiver/{$event_id}"
];

foreach($links as $link) {
	echo anchor($link['href'], $link['label'], $link['attrs']);
}

?></div>

<?php $this->endSection();
