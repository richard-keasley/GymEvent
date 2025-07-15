<div id="sender" class="m-0 p-1 alert alert-secondary">

<div class="row">
<div class="col-auto"> 
<button id="sse-play" type="button" class="btn btn-sm btn-primary bi bi-play-fill px-3"></button>
<button id="sse-pause" type="button" class="btn btn-sm btn-primary bi bi-stop-fill px-3 d-none" onclick="sse.send('pause')"></button>
</div>

<div class="col-auto">
<div class="bg-dark text-light text-center fw-bold" style="min-width:4.7em; line-height:1.9em" id="ssetimer">0:00</div>
</div>

<div class="col-auto"><?php
$href = "control/player/receiver/{$event_id}";
$label = '<span class="bi bi-broadcast"></span>';
$attrs = [
	'class' => "px-3 btn btn-outline-secondary btn-sm",
	'title' => "View music receiver",
	'target' => "_blank",
]; 
echo anchor($href, $label, $attrs); 
?></div>

</div>

<p>ready...</p>
</div>
<?php echo new \App\Views\js\timer('ssetimer'); ?>
<script>

const sse = {
	
buttons: {
	play: document.querySelector('#sender #sse-play'),
	pause: document.querySelector('#sender #sse-pause'),
},

send: function(state, params={}) {
	params['state'] = state;

	// send request	
	var api = '<?php echo site_url("api/music/sse");?>';
	$.post(api, securepost(params))
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
		var timer = setInterval(function() {
			$('#ssetimer').text(ssetimer.format());
		}, 1000);
		ssetimer.start(0, timer);
		
		this.buttons.play.classList.add('d-none');
		this.buttons.pause.classList.remove('d-none');
		break;
		
		case 'pause':
		ssetimer.reset();
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

}

</script>
