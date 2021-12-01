<?php $this->extend('default');
$table = new \CodeIgniter\View\Table();
$template = ['table_open' => '<table class="table">'];
$table->setTemplate($template);

$this->section('content'); ?>
<?php 
$get_var = $tt_lib::get_var('progtable');
$progtable = $get_var ? $get_var->value : [] ;
$exes = [];
if($progtable) {
	$exes = $progtable[0];
	$table->setHeading($exes);
	array_shift($exes);
}
else { ?>
<p class="alert-danger">Programme appears to be empty</p>
<?php } 

$get_var = $tt_lib::get_var('teams');
$teams = $get_var ? $get_var->value : [] ;
if(!$teams) { ?>
<p class="alert-danger">Teams are not set-up</p>
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
		$tr[$exe] = $track->button();
			
		
	}
	$tbody[] = $tr;
}
echo $table->generate($tbody);
$this->endSection(); 

$this->section('top'); ?>
<div class="toolbar sticky-top row">
<div class="col-auto">
<?php echo \App\Libraries\View::back_link($back_link); ?>
</div>
<div class="col-auto"><div id="player">
<audio style="width:25em;" controls></audio> 
<p class="m-0 p-0">ready&hellip;</p>
<p class="m-0 p-0">source&hellip;</p>
</div></div>
</div>
<script>
$(function() {
var player = $('#player')[0];
var playeraudio = $('#player audio')[0];
var playermsg = $('#player p')[0];
var playersrc = $('#player p')[1];

$('button[name=trk]').click(function() {
	var track_url = this.dataset.url;
	if(!track_url) return;
	playersrc.innerHTML = track_url;
	playermsg.innerHTML = 'Playing ' + this.value;
	player.className = 'alert-success';
	playeraudio.src = track_url;
	playeraudio.play();
});

playeraudio.addEventListener("error", function(e) {
	var msg = '' ;
	switch(e.target.error.code) {
		case e.target.error.MEDIA_ERR_ABORTED: 
		msg = 'Download aborted'; 
		break;
		case e.target.error.MEDIA_ERR_NETWORK: 
		msg = 'Network error'; 
		break;
		case e.target.error.MEDIA_ERR_DECODE: 
		msg = 'Decoding error'; 
		break;
		case e.target.error.MEDIA_ERR_SRC_NOT_SUPPORTED: 
		msg = 'No decoder available'; 
		break;
		default: 
		msg = 'An unknown error occurred.';
    }
	playermsg.innerHTML = msg;
	player.className = 'alert-danger';
});

});
</script>

<?php $this->endSection();
