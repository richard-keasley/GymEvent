<?php
// default options
$options = [
	'audio' => false,
	'ui' => true,
	'btns' => false,
];
// read options from request
foreach(array_keys($options) as $key) {
	$val = $this->renderVars['options'][$key] ?? null ;
	if($val!==null) $options[$key] = $val;
}

# d($this->renderVars['options']);
# d($options);

?>
<div id="playtrack">
<?php
$attrs = $options['audio'] ? 
	'preload="auto" class="w-100" controls="controls"' : 
	'preload="auto" class="d-none"' ;
echo "<audio {$attrs}></audio>";

$ui = $options['ui'] ? '' : 'd-none' ;

?>
<div id="pt-ui" class="<?php echo $ui;?>">

<div class="d-flex">
<?php if($options['btns']) { ?>
<span id="pt-btns">
<button id="pt-play" type="button" class="btn btn-sm btn-info" onclick="playtrack.play()"><span class="bi bi-play-fill"></span></button>
<button id="pt-pause" type="button" class="btn btn-sm btn-info" onclick="playtrack.pause(1000)"><span class="bi bi-pause-fill"></span></button>
</span>
<?php } ?>
<span id="pt-info"></span>
<span id="pt-duration"></span>
<span id="pt-time-text"></span>
</div>

<div id="pt-progress" class="position-relative">
<div id="pt-buffer" class="progress position-absolute top-0 w-100">
<div class="progress-bar progress-bar-striped bg-warning"></div>
</div>
<div id="pt-time-progbar" class="progress position-absolute top-0 w-100 bg-transparent">
<div class="progress-bar progress-bar-striped progress-bar-animated"></div>
</div>
</div>

</div>

</div>

<script>
const playtrack = {
	
audio: document.querySelector('#playtrack audio'),
ui: document.querySelector('#playtrack #pt-ui'),
info: document.querySelector('#playtrack #pt-info'),
time: {
	text: document.querySelector('#playtrack #pt-time-text'),
	progbar: document.querySelector('#playtrack #pt-time-progbar .progress-bar'),
},
duration: document.querySelector('#playtrack #pt-duration'),
buffer: document.querySelector('#playtrack #pt-buffer .progress-bar'),
options: <?php echo json_encode($options); ?>,
buttons: <?php echo json_encode(\App\Libraries\Track::BUTTONS); ?>,
btns: {
	play: document.querySelector('#playtrack #pt-play'),
	pause: document.querySelector('#playtrack #pt-pause'),
},
active_btn: 0,
autoplay: true,

init: function() {
	if(this.options.btns) {
		this.btns.play.classList.remove('d-none');
		this.btns.pause.classList.add('d-none');
	}
	
	this.audio.currentTime = 0;
	this.time.text.innerHTML = '<i class="bi bi-music-note-beamed"></i>';
	this.time.progbar.style = 'width:0';
	this.buffer.style = 'width:0';

	if(this.active_btn) {
		this.active_btn.className = this.buttons.repeat;
	}
	this.active_btn = 0;
	
	if(this.autoplay) {
		this.duration.innerHTML = '';
		this.message('ready&hellip;', 'light');
	}
},

button: function(el) {	
	if(this.active_btn===el) {
		// fade current track
		this.pause(1000);
		return;
	}
	// stop current track
	this.pause();
		
	// set active button
	this.active_btn = el;
	// play requested track
	this.load(el.dataset.url ?? '');
},

load: function(track_url, autoplay=1) {
	if(!track_url) return;
		
	var temp = track_url.split('/').pop(); // filename
	var html = temp.split('?')[0]; // remove query
	html = html.replace(/^0+/, ''); // trim leading zeros
	html = html.replace('.', ' (') + ')'; // place extension in bracket
	html = html.replace('_', ' ');
	this.message(html, 'success');
	
	// playtrack.init();
	this.audio.src = track_url;
	this.autoplay = autoplay;
	if(autoplay) this.play();
},

play: function() {
	if(this.options.btns) {
		this.btns.play.classList.add('d-none');
		this.btns.pause.classList.remove('d-none');
	}
	
	this.audio.volume = 1;
	this.audio.muted = false;
	this.audio.play().catch(error => {
		this.error(error.name);
	});
},

pause: function(fade=0) {
	$('#playtrack audio').animate({volume: 0}, fade, function() {
		this.audio.pause();
		$('#playtrack audio').animate({volume: 1}, 1);
		this.init();
	}.bind(this));
},

message: function(html, alert='danger') {
	if(!this.options.ui) return;
	if(html!==null) this.info.innerHTML = html;
	this.ui.className = 'p-1 m-0 alert alert-' + alert;
},

displaytime: function(secs) {
	var minutes = Math.floor(secs / 60);
	var seconds = Math.floor(secs % 60);
	return seconds < 10 ? 
		`${minutes}:0${seconds}` : 
		`${minutes}:${seconds}` ;
},

timeupdate: function() {
	if(this.audio.paused) return;
	
	var secs = this.audio.currentTime;
	var pc = secs / this.audio.duration * 100;
	this.time.progbar.style = 'width:'+pc+'%';
	this.time.text.innerHTML = this.displaytime(secs);	
		
	secs = (this.audio.buffered.length) ? 
		 this.audio.buffered.end(0) : 
		 0 ;
	pc = secs / this.audio.duration * 100;
	this.buffer.style = 'width:'+pc+'%';
	
	this.buffer.className = (pc>99) ? 
		'progress-bar progress-bar-striped bg-success' : 
		'progress-bar progress-bar-striped bg-warning' ;
},

error: function(error_name, notify=true) {
	const messages = {
		media_1: 'Download aborted',
		media_2: 'Network error',
		media_3: 'Decoding error',
		media_4: 'No decoder', 
		http_404: 'Track not found',
		NotAllowedError: 'Allow automatic playback',
	};
	
	var retval = {
		name: error_name,
		message: messages[error_name] ?? `unkown error (${error_name})`,
	};
	
	this.init();
	if(notify) {
		this.message(`<span title="${retval.name}">${retval.message}</span>`);
	}
	return retval;
},

};

$(function() {

playtrack.init();

playtrack.audio.ontimeupdate = function() {
	playtrack.timeupdate();
}

playtrack.audio.onloadedmetadata = function() {
	var secs = playtrack.audio.duration;
	playtrack.duration.innerHTML = playtrack.displaytime(secs);
}

playtrack.audio.onplay = function() {
	// is a button active?
	if(playtrack.active_btn) {
		playtrack.active_btn.className = playtrack.buttons.pause + ' playing';	
	}
}

playtrack.audio.onended = function() {
	playtrack.init();
}

playtrack.audio.onerror = function(event) {
	var media_error = event.target.error;
	
	// look for http errors 
	const regex = /^([0-9]{3}):/;
	var http = media_error.message.match(regex);
	if(http) http = http[1] ?? null ;
	
	// use media error if no http error
	var error_name = http ? 
		'http_' + http : 
		'media_' + media_error.code ;
	var error = playtrack.error(error_name, 0);
	
	// rewrite error message
	playtrack.message(`<a href="${event.target.src}" title="try to download this track" target="music">${error.message}</a>`);
};

});
</script>
