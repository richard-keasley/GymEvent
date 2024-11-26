<div id="sender" class="m-0 p-1 alert alert-secondary">

<div class="row">
<div class="col-auto"><?php
$attrs = ['class' => "me-1"]; 
echo anchor("control/player/receiver/{$event_id}", 'Receiver', $attrs); 
?></div>
<div class="col-auto"> 
<button id="sse-play" type="button" class="btn btn-sm btn-primary bi bi-play-fill px-3"></button>
<button id="sse-pause" type="button" class="btn btn-sm btn-primary bi bi-stop-fill px-3" onclick="sse.send('pause')"></button>
</div>
<div class="col-auto">
<?php echo new \App\Views\Htm\Timer('ssetimer'); ?>
</div>
</div>

<p>ready...</p>
</div>
<script>
const sse = {

send: function(state, params={}) {
	params['state'] = state;
	params['<?php echo csrf_token();?>'] = '<?php echo csrf_hash(); ?>';
	// console.log(params);

	// send request	
	var api = '<?php echo site_url("api/music/sse");?>';
	$.post(api, params)
	.done(function(response) {
		// console.log(response);
		sse.message(response.label, response.state);
				
	})
	.fail(function(jqXHR) {
		sse.message(get_error(jqXHR));
	});
	
	// update timer
	switch(state) {
		case 'play':
		this.timer.start();
		break;
		case 'pause':
		this.timer.reset();
		break;
	}
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
},

timer: new timer('ssetimer')

}
</script>
