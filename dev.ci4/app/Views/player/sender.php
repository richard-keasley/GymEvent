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
<div class="col-auto bg-dark text-light text-center" style="width:4.7em">
	<span class="fw-bold align-middle" id="sse-timer"></span>
</div>
</div>

<p>ready...</p>
</div>
<script>
$(function() {
sse.timer.reset();
});

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
		sse.timer.start();
		break;
		case 'pause':
		sse.timer.reset();
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

timer: {
	timer: null,
	el: $('#sse-timer'),
	start: function() {
		var secs = 0;
		sse.timer.timer = setInterval(function() {
			secs++;
			sse.timer.show(secs);
		}, 1000);
	},
	reset: function() {
		if(sse.timer.timer) clearInterval(sse.timer.timer);
		sse.timer.show(0);
	},
	show: function(secs) {
		var mins = Math.floor(secs/60); 
		secs = secs % 60;
		mins = mins.toString().length < 2 ? '0' + mins : mins;
		secs = secs.toString().length < 2 ? '0' + secs : secs;
		// console.log(mins + ':' + secs);
		sse.timer.el.text(mins + ':' + secs);
	}
}

}
</script>
