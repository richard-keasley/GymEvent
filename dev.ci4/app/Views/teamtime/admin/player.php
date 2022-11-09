<?php $this->extend('default');
use \App\Libraries\Teamtime as tt_lib;

$this->section('content');

$table = \App\Views\Htm\Table::load('responsive');
$progtable = tt_lib::get_value('progtable');

$exes = [];
if($progtable) {
	$exes = $progtable[0];
	$thead = [];
	foreach($exes as $exe) {
		$thead[] = \App\Views\Htm\Table::centre($exe);
	}
	$table->setHeading($thead);
	array_shift($exes);
}
else { ?>
<p class="alert alert-danger">Programme appears to be empty</p>
<?php } 

$teams = tt_lib::get_value('teams');
if(!$teams) { ?>
<p class="alert alert-danger">Teams are not set-up</p>
<?php }

$event_id = tt_lib::get_var('settings', 'event_id');
$track = new \App\Libraries\Track();
$track->event_id = $event_id;

$mdl_events = new \App\Models\Events;
$event = $mdl_events->find($event_id);
$title = $event ? $event->title : 'Event not found' ;
printf('<h2>%s</h2>', $title);

#printf('<p>%s</p>', $track->urlpath());

$tr = []; $tbody = [];
foreach($teams as $team) {
	$tr[0] = implode('. ', $team);
	$track->entry_num = $team[0];
	foreach($exes as $exe) {
		$track->exe = $exe;
		$tr[$exe] = $track->playbtn(['player']);
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
<script>
$(function(){

$('button[name=trk]').click(function() {
	var track_url = this.dataset.url;
	if(!track_url) return;
	playtrack.load(track_url); 
});
	
});
</script>

</div>

<?php $this->endSection();
