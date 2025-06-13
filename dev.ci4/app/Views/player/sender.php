<div id="sender" class="m-0 p-1 alert alert-secondary">

<div class="row">
<div class="col-auto"> 
<button id="sse-play" type="button" class="btn btn-sm btn-primary bi bi-play-fill px-3"></button>
<button id="sse-pause" type="button" class="btn btn-sm btn-primary bi bi-stop-fill px-3 d-none" onclick="sse.send('pause')"></button>
</div>
<div class="col-auto">
<?php echo new \App\Views\Htm\Timer('ssetimer'); ?>
</div>
<div class="col-auto"><?php
$attrs = [
	'class' => "px-3 btn btn-outline-secondary btn-sm",
	'title' => "View music receiver",
	'target' => "ssereceiver",
]; 
echo anchor("control/player/receiver/{$event_id}", '<span class="bi bi-broadcast"></span>', $attrs); 
?></div>
</div>

<p>ready...</p>
</div>
<script>
const sse = {
	
buttons: {
	play: document.querySelector('#sender #sse-play'),
	pause: document.querySelector('#sender #sse-pause'),
},

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
		state = response.state;	
	})
	.fail(function(jqXHR) {
		sse.message(get_error(jqXHR));
	});
	
	// update display
	switch(state) {
		case 'play':
		this.timer.start();
		this.buttons.play.classList.add('d-none');
		this.buttons.pause.classList.remove('d-none');
		break;
		
		case 'pause':
		this.timer.reset();
		this.buttons.pause.classList.add('d-none');
		this.buttons.play.classList.remove('d-none');
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
