<div id="playtrack" class="w-100 flex-column">
<audio class="w-100" controls="controls"></audio> 
<p class=""></p>
<script>
const playtrack = {
	load: function(track_url, autoplay=1) {
		playtrack.pause();
		if(track_url) {
			var temp = track_url.split('/').pop(); // filename
			var html = temp.split('?')[0]; // remove query
			html = html.replace(/^0+/, ''); // trim leading zeros
			html = html.replace('.', ' (') + ')'; // separate extension into bracket
			html = html.replace('_', ' ');
			playtrack.msg(html, 'success');
			playtrack.player.attr('src', track_url);
			if(autoplay) {
				playtrack.player.trigger('play');
			}
		}
	},
	pause: function() {
		playtrack.msg('ready&hellip;', 'light');
		playtrack.player.trigger('pause');
	},
	msg: function(html, alert) {
		playtrack.message.innerHTML = html;
		playtrack.message.className = 'p-1 my-0 alert alert-' + alert;
	},
	player: $('#playtrack audio'),
	message: $('#playtrack p')[0]
};

$(function() {
playtrack.pause();

playtrack.player.on("error", function(e) {
	var html;
	switch(e.target.error.code) {
		case e.target.error.MEDIA_ERR_ABORTED: html = 'Download aborted'; break;
		case e.target.error.MEDIA_ERR_NETWORK: html = 'Network error'; break;
		case e.target.error.MEDIA_ERR_DECODE: html = 'Decoding error'; break;
		case e.target.error.MEDIA_ERR_SRC_NOT_SUPPORTED: html = 'No decoder available'; break;
		default: html = 'Unknown error';
	}
	html = '<a href="' + e.target.src + '" title="try to download this track" target="music">'+html+'</a>';
	playtrack.msg(html, 'danger');
});
	
});
</script>
</div>