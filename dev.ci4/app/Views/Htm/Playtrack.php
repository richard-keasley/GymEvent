<div id="playtrack" class="w-100 d-flex flex-column">
<audio class="w-100" preload="auto" controls="controls"></audio> 
<p class=""></p>
<script>
const playtrack = {
load: function(track_url, autoplay=1) {
	playtrack.pause();
	if(track_url) {
		var temp = track_url.split('/').pop(); // filename
		var html = temp.split('?')[0]; // remove query
		html = html.replace(/^0+/, ''); // trim leading zeros
		html = html.replace('.', ' (') + ')'; // place extension in bracket
		html = html.replace('_', ' ');
		playtrack.msg(html, 'warning');
		playtrack.audio.src = track_url;
		playtrack.audio.muted = false;
		playtrack.audio.volume = 1;
		playtrack.audio.load();	
		playtrack.autoplay = autoplay;
	}
},

pause: function() {
	playtrack.msg('ready&hellip;', 'light');
	playtrack.audio.pause();
},

msg: function(html, alert) {
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