<div id="playtrack" class="w-100 d-flex flex-column">
<audio class="w-100" preload="auto" controls="controls"></audio> 
<p class=""></p>
<script>
const playtrack = {
	
active_btn: 0,
	
button: function(el) {
	var track_url = el.dataset.url;
	if(!track_url) return false;
	
	var BUTTONS = <?php echo json_encode(\App\Libraries\Track::BUTTONS); ?>;
		
	// is a button active?
	if(playtrack.active_btn) {
		playtrack.active_btn.className = BUTTONS.repeat;
		if(playtrack.active_btn===el) {
			// fade current track
			playtrack.pause(1000);
			playtrack.active_btn = 0;
			return true;
		}
		else {
			// jump to requested track
			playtrack.pause();
		}
	}
	
	// play requested track
	playtrack.load(track_url);
		
	// set active button
	playtrack.active_btn = el;
	playtrack.active_btn.className = BUTTONS.pause;
	return true;
},	
	
load: function(track_url, autoplay=1) {
	playtrack.pause();    

	if(track_url) {
		var temp = track_url.split('/').pop(); // filename
		var html = temp.split('?')[0]; // remove query
		html = html.replace(/^0+/, ''); // trim leading zeros
		html = html.replace('.', ' (') + ')'; // place extension in bracket
		html = html.replace('_', ' ');
		playtrack.msg(html, 'warning');
		
		playtrack.audio.volume = 1;
		playtrack.audio.src = track_url;
		playtrack.audio.muted = false;
		playtrack.audio.load();	
		playtrack.autoplay = autoplay;
	}
}, 

pause: function(fade=0) {
	$('#playtrack audio').animate({volume: 0}, fade, function() {
        playtrack.audio.pause();
		$('#playtrack audio').animate({volume: 1}, 1);		
	});
	playtrack.msg('ready&hellip;', 'light');	
},

play: function() {
	playtrack.audio.play().catch(error => {
		// Autoplay was blocked. Do something
		playtrack.msg("Check autoplay is allowed on this page");
	});
},

msg: function(html, alert='danger') {
	playtrack.message.innerHTML = html;
	playtrack.message.className = 'p-1 my-0 alert alert-' + alert;
},

audio: $('#playtrack audio')[0],
message: $('#playtrack p')[0],
autoplay: 1
};

$(function() {
playtrack.pause();

playtrack.audio.onerror = (event) => {
	var html;
	switch(event.target.error.code) {
		case event.target.error.MEDIA_ERR_ABORTED: html = 'Download aborted'; break;
		case event.target.error.MEDIA_ERR_NETWORK: html = 'Network error'; break;
		case event.target.error.MEDIA_ERR_DECODE: html = 'Decoding error'; break;
		case event.target.error.MEDIA_ERR_SRC_NOT_SUPPORTED: html = 'No decoder available'; break;
		default: html = 'Unknown error';
	}
	html = '<a href="' + event.target.src + '" title="try to download this track" target="music">' + html + '</a>';
	playtrack.msg(html, 'danger');
};

playtrack.audio.oncanplaythrough = (event) => {
	var html = playtrack.message.innerHTML;
	playtrack.msg(html, 'success');
	if(playtrack.autoplay) playtrack.audio.play();
};
	
});
</script>
</div>