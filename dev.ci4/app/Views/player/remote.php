<div id="remoteplayer">
<div>
<?php echo anchor('control/player/auto', 'remote player'); ?>&nbsp;&nbsp;&nbsp; 
<button type="button" class="btn btn-sm btn-primary bi bi-play-fill" onclick="remote_music('play')"></button>
<button type="button" class="btn btn-sm btn-primary bi bi-stop-fill" onclick="remote_music('stop')"></button>
</div>
<p class="m-0">ready&hellip;</p>
<script>
const $remoteplayer = $('#remoteplayer')[0];
const $remoteplayer_msg = $('#remoteplayer p')[0];

function remote_music(state) {
	url = '<?php echo site_url("/api/music/set_remote");?>';
	postvar = {
		event: event_id,
		entry: progtable[runvars['row']][runvars['col']],
		exe: progtable[0][runvars['col']],
		state: state
	};
	postvar[csrf_token] = csrf_hash;
	
	// console.log(postvar);
	$.post(url, postvar)
	.done(function(response) {
		// console.log(response);
		$remoteplayer_msg.innerHTML = response.state + ': ' + response.label;
		$remoteplayer.className = 'm-0 p-1 alert alert-success';
	})
	.fail(function(jqXHR) {
		$remoteplayer_msg.innerHTML = get_error(jqXHR);
		$remoteplayer.className = 'm-0 p-1 alert alert-danger';
	});
}
</script>
</div>