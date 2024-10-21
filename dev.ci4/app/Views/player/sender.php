<div id="sender" class="m-0 p-1 alert alert-secondary">
<div>
<?php 
echo anchor("control/player/receiver/{$event_id}", 'Receiver'); ?>&nbsp;&nbsp;&nbsp; 
<button type="button" id="sse-play" class="btn btn-sm btn-primary bi bi-play-fill"></button>
<button type="button" class="btn btn-sm btn-primary bi bi-stop-fill" onclick="sse.send('pause')"></button>
</div>
<p>ready...</p>
</div>
<script>
const sse = {

send: function(state, params={}) {
	params['state'] = state;
	params['<?php echo csrf_token();?>'] = '<?php echo csrf_hash(); ?>';
	// console.log(params);
	
	var api = '<?php echo site_url("api/music/sse");?>';
	$.post(api, params)
	.done(function(response) {
		// console.log(response);
		sse.message(response.label, response.state);
	})
	.fail(function(jqXHR) {
		sse.message(get_error(jqXHR));
	});
},

message: function(message, state='error') {
	var alert = 'danger';
	switch(state) {
		case 'error':
		break;
		
		case 'pause':
		message = 'ready...';
		alert = 'secondary';
		break;
			
		default:
		message = state + ': ' + message;
		alert = 'success';
	}
	
	
	$('#sender p').text(message);
	$('#sender')[0].className = 'm-0 p-1 alert alert-' + alert;	
}

}
</script>
