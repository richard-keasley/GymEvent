<?php $this->extend('default'); 

# $action = 'save';

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
# d($action);

?>
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
<div class="toolbar">
<?php
$links = [];
$buttons = [];

if($action=='save') {
	$links[] = [
		'label' => '<i class="bi bi-globe"></i>',
		'attrs' => [
			'title' => "View media player online",
			'class' => "btn btn-outline-secondary"
		],
		'href' => "control/player/view/{$event->id}"
	];
}

if($action=='view') {
	$links[] = [
		'label' => '<i class="bi bi-download"></i>',
		'attrs' => [
			'title' => "Save media player to local device",
			'class' => "btn btn-primary"
		],
		'href' => "control/player/view/{$event->id}/save"
	];

	$links[] = [
		'label' => '<i class="bi bi-gear-fill"></i>',
		'attrs' => [
			'title' => "Setup player",
			'class' => "btn btn-outline-secondary"
		],
		'href' => "control/player/edit/{$event->id}"
	];
	$links[] = [
		'label' => '<span class="bi bi-broadcast"></span>',
		'attrs' => [
			'title' => "Start player receiver",
			'class' => "btn btn-outline-secondary",
			'target' => "_blank",
		],
		'href' => "control/player/receiver/{$event->id}"
	];
	
	$buttons[] = [
		'label' => '<i class="bi bi-broadcast-pin"></i>',
		'attrs' => [
			'class' => "btn btn-outline-primary playremote",
			'onclick' => 'player.setremote(this)',
			'title' => "remote player",
		]
	];
}

foreach($links as $link) {
	echo anchor($link['href'], $link['label'], $link['attrs']);
}
$format = '<button %s>%s</button>';
foreach($buttons as $btn) {
	printf($format, stringify_attributes($btn['attrs']), $btn['label']);
}
?>
</div>
<?php $this->endSection(); 

$this->section('top');
echo $this->include('player/local');
?>
<div class="sticky-top pb-1">
<?php echo $this->include('Htm/Playtrack'); ?></div>
<div id="sseresponse" class="m-0 mb-1 p-1 alert alert-light" style="display:none;">ready...</div>
<script>
const player = {
active_tab: null,
remote: false,
els: {
	remote: $('#sseresponse'),
	local: $('#playtrack')
},

setremote: function(btn) {
	this.remote = btn.classList.contains('playremote');
	if(this.remote) {
		btn.className = "btn btn-danger";
		this.els.local.hide();
		this.els.remote.show();		
	} 
	else {
		btn.className = "btn btn-outline-primary playremote";
		this.els.local.show();
		this.els.remote.hide();
	}		
},

playremote: function(btn) {
	var playing = btn.classList.contains('playing');
	var newstate = playing ? 'pause' : 'play' ;
	
	// clear all current playing buttons 
	$('button.playing').each(function() {
		this.className = playtrack.buttons.repeat;
	});
	// set current button
	if(newstate=='play') {
		btn.className = playtrack.buttons.pause + ' playing';	
	} 
	else {
		btn.className = playtrack.buttons.repeat;	
	}
			
	// send SSE request	
	params = {
		state: newstate,
		url: btn.dataset.url
	};
	var api = '<?php echo site_url("api/music/ssetrack");?>';
	$.post(api, securepost(params))
	.done(function(response) {
		// console.log(response);
		var text = [];
		Object.keys(response).forEach(function(key) {
			var value = response[key];
			if(key=='id') value = null;
			if(value) text.push(value);
		});
		player.els.remote.html(text.join(', '));
	})
	.fail(function(jqXHR) {
		var message = get_error(jqXHR);
		console.error(message);
		player.els.remote.html(`<strong class="text-danger">${message}</strong>`);
	});
}

};

$(function() {
$('button[name=trk]').click(function() {
	if(player.remote) player.playremote(this);
	else playtrack.button(this);
	
	// highlight selected tab
	if(player.active_tab) {
		player.active_tab.classList.remove('text-bg-success');
	}
	var acc_item = this.closest('.accordion-item');
	if(acc_item) {
		player.active_tab = acc_item.querySelector('.accordion-button');
		player.active_tab.classList.add('text-bg-success');
	}
	else {
		player.active_tab = null;
	}
	
});
});
</script>
<?php $this->endSection(); 
