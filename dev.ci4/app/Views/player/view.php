<?php $this->extend('default'); 

$this->section('content'); 
#d($event);
#d($event->player);
$track = new \App\Libraries\Track;
$track->event_id = $event->id;
?>

<div class="toolbar sticky-top">
<div id="playtrack" class="flex-column">
<audio controls></audio> 
<p>ready&hellip;</p>
</div>
<script>
<?php echo \App\Libraries\Track::js_buttons();?>

$(function() {
var $player = $('#playtrack audio');
var playermsg = $('#playtrack p')[0];
var play_button = 0;
var active_tab = 0;

$('button[name=trk]').click(function() {
	var track_url = this.dataset.url;
	if(!track_url) return;
		
	if(active_tab) {
		active_tab.classList.remove('bg-success');
		active_tab.classList.remove('text-light');
	}
	active_tab = this.parentElement.parentElement.parentElement.querySelector('.accordion-button');
	active_tab.classList.add('bg-success');
	active_tab.classList.add('text-light');
	
	if(play_button) {
		play_button.className = BUTTON_REPEAT;
		playermsg.innerHTML = 'ready&hellip;';
		playermsg.className = '';
		$player.trigger('pause');
		if(play_button.value==this.value) {
			play_button = 0;
			return;
		}
	}
		
	play_button = this;
	playermsg.innerHTML = 'Playing ' + this.value;
	playermsg.className = 'alert-success';
	this.className = BUTTON_PAUSE;
	$player.attr('src', track_url);
	$player.trigger('play');
});

$player.on("error", function(e) {
	var msg = '' ;
	switch(e.target.error.code) {
		case e.target.error.MEDIA_ERR_ABORTED: msg = 'Download aborted'; break;
		case e.target.error.MEDIA_ERR_NETWORK: msg = 'Network error'; break;
		case e.target.error.MEDIA_ERR_DECODE: msg = 'Decoding error'; break;
		case e.target.error.MEDIA_ERR_SRC_NOT_SUPPORTED: msg = 'No decoder available'; break;
		default: msg = 'An unknown error occurred.';
    }
	msg = '<a href="' + e.target.src + '" title="try to download this track" target="music">'+msg+'</a>';
	playermsg.innerHTML = msg;
	playermsg.className = 'alert-danger';
});

});
</script>
</div>

<?php 
$pattern = $track->filepath() .  '*';
$glob = glob($pattern); 
$notlisted = [];
foreach($glob as $filepath) $notlisted[] = basename($filepath);

foreach($event->player as $round_key=>$round) { 
$round_num = $round_key + 1;
$id = sprintf('round%u', $round_key);
$track->exe = $round['exe'];
?>
<div class="border my-1">
	<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $id;?>"><?php printf('%u. %s / %s', $round_num, $round['title'], $round['exe']);?></button>
  	<div class="collapse card card-body show" id="<?php echo $id;?>">
		<?php if($round['description']) printf('<p>%s</p>', $round['description']);?>
		<div class="playlist"><?php foreach($round['entry_nums'] as $entry_num) {
			$track->entry_num = $entry_num;
			echo $track->button();
			$filekey = array_search($track->filename(), $notlisted);
			if($filekey!==false) unset($notlisted[$filekey]);
		} ?></div>
	</div>
</div>
<?php } 
if($notlisted) { ?>
<div class="border my-1 p-1 alert-danger">
<h6>Tracks saved, but not listed</h6>
<?php foreach($notlisted as $filename) { 
	$track->setFilename($filename);
	echo $track->button();
} ?>
</div>
<?php } ?>
<?php $this->endSection(); 

$this->section('bottom');?>
<div  class="toolbar">
	<?php printf('<a href="%s" class="bi bi-gear-fill btn btn-outline-secondary" title="Setup event"></a>', base_url("control/player/edit/{$event->id}"));?>
</div>
<?php 
$this->endSection(); 
