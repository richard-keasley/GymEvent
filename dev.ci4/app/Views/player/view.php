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
<?php echo $this->include('player/js_buttons');?>
<script>
var active_tab = 0; 

$(function() {
$('button[name=trk]').click(function() {
	var success = playbutton(this);
	if(!success) return;
	
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

$this->section('bottom'); ?>
<footer class="toolbar">
<?php 
$label = '<i class="bi bi-gear-fill"></i>';
$attrs = [
	'title' => "Setup event",
	'class' => "btn btn-outline-secondary"
];
echo anchor("control/player/edit/{$event->id}", $label, $attrs);

$label = '<i class="bi bi-download"></i>';
$attrs = [
	'title' => "Save media player to local device",
	'class' => "btn btn-outline-secondary"
];
echo anchor("control/player/savepage/{$event->id}", $label, $attrs);	

$attrs = [
	'title' => "Start auto-player",
	'class' => "btn btn-outline-secondary"
];
echo anchor("control/player/auto", 'Auto', $attrs);
?>
</footer>
<?php $this->endSection(); 
