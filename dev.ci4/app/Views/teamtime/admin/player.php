<?php $this->extend('default');
$table = \App\Views\Htm\Table::load('default');

$this->section('content');

$get_var = $tt_lib::get_var('progtable');
$progtable = $get_var ? $get_var->value : [] ;
$exes = [];
if($progtable) {
	$exes = $progtable[0];
	$table->setHeading($exes);
	array_shift($exes);
}
else { ?>
<p class="alert alert-danger">Programme appears to be empty</p>
<?php } 

$get_var = $tt_lib::get_var('teams');
$teams = $get_var ? $get_var->value : [] ;
if(!$teams) { ?>
<p class="alert alert-danger">Teams are not set-up</p>
<?php }

$event_id = $tt_lib::get_var('settings', 'event_id');
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
<div class="toolbar sticky-top row">
<div class="col-auto">
<?php echo \App\Libraries\View::back_link('control/teamtime'); ?>
</div>

<div class="col-auto">
<?php echo $this->include('Htm/Playtrack'); ?>
</div>
<script>
$(function(){

$('button[name=track]').click(function() {
	var track_url = this.dataset.url;
	if(!track_url) return; 
	playtrack.play(track_url);
});
	
});
</script>

</div>

<?php $this->endSection();
