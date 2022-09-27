<div id="playtrack" class="w-100 flex-column">
<audio class="w-100" controls="controls"></audio> 
<p class="p-1 my-0"></p>
<script>
const playtrack = {
	play: function(track_url) {
		playtrack.pause();
		if(track_url) {
			var temp = track_url.split('/').pop();
			var html = temp.split('?')[0];
			html = html.replace('.', ' (') + ')';
			html = html.replace('_', ' ');
			playtrack.msg(html, 'success');
			playtrack.player.attr('src', track_url);
			playtrack.player.trigger('play');
		}
	},
	pause: function() {
		playtrack.msg('<span>ready&hellip;</span>', 'light');
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