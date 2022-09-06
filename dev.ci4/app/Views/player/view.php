<?php $this->extend('default'); 

$this->section('content'); 
$track = new \App\Libraries\Track;
$track->event_id = $event->id;

// get all stored tracks
$notlisted = [];
$files = $track->files(true);
foreach($files as $file) $notlisted[] = $file->getFilename();

# d($track);
# d($notlisted);
# d($event);
# d($event->player);
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

playermsg.className = 'p-1 my-0 text-bg-light';

$('button[name=trk]').click(function() {
	var track_url = this.dataset.url;
	if(!track_url) return;
		
	if(active_tab) {
		active_tab.classList.remove('text-bg-success');
	}

	var acc_item = this.parentElement.parentElement.parentElement.parentElement;
	if(acc_item.className=='accordion-item') {
		active_tab = acc_item.querySelector('.accordion-button');
		active_tab.classList.add('text-bg-success');
	}
	else {
		active_tab = 0;
	}
	
	if(play_button) {
		play_button.className = BUTTON_REPEAT;
		playermsg.innerHTML = 'ready&hellip;';
		playermsg.className = 'p-1 my-0 text-bg-light';
		$player.trigger('pause');
		if(play_button.value==this.value) {
			play_button = 0;
			return;
		}
	}
		
	play_button = this;
	playermsg.innerHTML = 'Playing ' + this.value;
	playermsg.className = 'p-1 my-0 text-bg-success';
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
	playermsg.className = 'p-1 my-0 alert alert-danger';
});

});
</script>
</div>

<div class="accordion">
<?php 
foreach($event->player as $round_key=>$round) { 
$panel_id = sprintf('acc-panel%u', $round_key);
$track->exe = $round['exe'];

?>
<div class="accordion-item">

<div class="accordion-header">
	<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $panel_id;?>"><?php printf('%s / %s', $round['title'], $round['exe']);?></button>
</div>
  
<div class="accordion-collapse collapse" id="<?php echo $panel_id;?>">
	<div class="accordion-body">
	<?php if($round['description']) printf('<p>%s</p>', $round['description']);?>
	<div class="playlist">
	<?php foreach($round['entry_nums'] as $entry_num) {
		$track->entry_num = $entry_num;
		// list this track
		echo $track->button();
		// remove this track from notlisted array
		$filekey = array_search($track->filename(), $notlisted);
		if($filekey!==false) unset($notlisted[$filekey]);
	} ?>
	</div>
	</div>
</div>

</div>
<?php }
?>
</div>

<?php if($notlisted) { ?>
<div class="alert alert-danger">
<h6>Tracks saved, but not listed</h6>
<div class="playlist">
<?php foreach($notlisted as $filename) { 
	$track->setFilename($filename);
	echo $track->button();
} ?>
</div>
</div>
<?php } ?>
<?php $this->endSection(); 

$this->section('bottom');?>
<div class="toolbar">
	<?php printf('<a href="%s" class="bi bi-gear-fill btn btn-outline-secondary" title="Setup event"></a>', base_url("control/player/edit/{$event->id}"));?>
</div>

<?php 
$this->endSection(); 
