<iframe id="playtrack2" class="w-100" style="height:5rem" src="">
</iframe>
<script>
const playtrack2 = {
	base_url: '<?php echo site_url('music/track');?>',
	play: function(event_id, entry_num, exe, autoplay) {
		var array = [
			playtrack2.base_url,
			event_id, 
			entry_num, 
			exe, 
			autoplay
		];
		playtrack2.player.src = array.join('/');
	},
	stop: function() {
		var array = [playtrack2.base_url, 0];
		playtrack2.player.src = array.join('/');
	},
	player: $('#playtrack2')[0]
};

$(function() {
playtrack2.stop();
});
</script>
	