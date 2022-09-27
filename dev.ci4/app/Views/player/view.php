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
<?php echo $this->include('Htm/Playtrack');?>
<script>
<?php echo \App\Libraries\Track::js_buttons();?>
var active_btn = 0;
var active_tab = 0; 

$(function() {
$('button[name=trk]').click(function() {
	var track_url = this.dataset.url;
	if(!track_url) return;
	
	// highlight selected tab
	if(active_tab) {
		active_tab.classList.remove('text-bg-success');
	}
	var acc_item = this.closest('.accordion-item');
	if(acc_item) {
		active_tab = acc_item.querySelector('.accordion-button');
		active_tab.classList.add('text-bg-success');
	}
	else {
		active_tab = 0;
	}
	
	// is a button active?
	if(active_btn) {
		active_btn.className = BUTTON_REPEAT;
		playtrack.pause();
		if(active_btn.title==this.title) {
			// stopping current track
			active_btn = 0;
			return;
		}
	}
	
	// play new track
	active_btn = this;
	active_btn.className = BUTTON_PAUSE;
	playtrack.play(track_url);
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
		echo $track->playbtn(['player']);
		$file = $track->file();
		if($file) {
			// remove this track from notlisted array
			$filekey = array_search($file->getFilename(), $notlisted);
			if($filekey!==false) unset($notlisted[$filekey]);
		}
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
	echo $track->playbtn(['player']);
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
