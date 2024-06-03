<?php $this->extend('default');
use \App\Libraries\Teamtime as tt_lib;

$this->section('content');

$table = \App\Views\Htm\Table::load('responsive');
$progtable = tt_lib::get_value('progtable');
$teams = tt_lib::get_value('teams');
$event_id = tt_lib::get_value('settings', 'event_id');
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

$mdl_events = new \App\Models\Events;
$event = $mdl_events->find($event_id);
$title = $event ? $event->title : 'Event not found' ;
printf('<h2>%s</h2>', $title);

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

$this->section('top'); ?>
<div class="toolbar sticky-top d-flex flex-wrap">
<div>
<?php echo \App\Libraries\View::back_link('control/teamtime'); ?>
</div>

<div style="width:100%; max-width:30em;">
<?php echo $this->include('Htm/Playtrack'); ?>
</div>

<div><?php
$attrs = [
	'title' => "Start auto-player",
	'class' => "btn btn-outline-secondary"
];
echo anchor("control/player/auto", 'Auto', $attrs);
?></div>
<?php echo $this->include('player/js_buttons');?>
	
<script>
$(function(){

$('button[name=trk]').click(function() {
	var track_url = playbutton(this);
});
	
});
</script>

</div>
<?php $this->endSection();
