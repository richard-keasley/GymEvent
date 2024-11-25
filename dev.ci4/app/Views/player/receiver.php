<?php $this->extend('default'); 

$this->section('content'); ?>
<div class="toolbar nav">
<button title="Start receiving from server" class="btn btn-success" type="button" onclick="receiver.open();" id="receiver_open"><span class="bi bi-play-fill"></span></button>
<button title="Stop receiver" class="btn btn-danger" type="button" onclick="receiver.close();" id="receiver_close" style="display:none"><span class="bi bi-stop-fill"></span></button>
<?php
$links = [];
$links[] = [
	'label' => 'Player',
	'attrs' => [
		'title' => "View player",
		'class' => "btn btn-outline-secondary"
	],
	'href' => "control/player/view/{$event->id}"
];

if($action=='view') {
$links[] = [
	'label' => '<i class="bi bi-download"></i>',
	'attrs' => [
		'title' => "Save receiver to local device",
		'class' => "btn btn-primary"
	],
	'href' => "control/player/receiver/{$event->id}/save"
];
}

foreach($links as $link) {
	echo anchor($link['href'], $link['label'], $link['attrs']);
}
?>
</div>

<p>This receiver plays tracks requested by the remote "sending" server 
(<code><?php echo $source_url;?></code>).</p>
<p>All tracks must be available in <code><?php echo $music_path;?></code>.</p>

<div id="rcvalert"></div>

<div class="border p-1 overflow-scroll" style="height:15em">
<ul id="messages" class="list list-unstyled"></ul>
</div>

<script>
const receiver = {
url: '<?php echo $source_url;?>',
base_url: '<?php echo $music_path;?>',
messages: $("#messages"),
source: null,
last_id: 0,

close: function() {
	receiver.source.close();
	receiver.last_id = 0;
	$('#receiver_open').show();
	$('#receiver_close').hide();
	playtrack.pause();
	var event = {data: 'Connection closed'};
	receiver.log(event);
},

open: function() {
	$('#receiver_open').hide();
	$('#receiver_close').show();
	$('#messages').text('');
	
	var event = {data: 'Opening ' + receiver.url};
	receiver.log(event);
	receiver.source = new EventSource(receiver.url);
	
	receiver.source.addEventListener("alert", (event) => {
		receiver.log(event);
		receiver.alert(event['data'] ?? 'error');
	}, false);

	receiver.source.addEventListener("pause", (event) => {
		receiver.log(event);
		if(!receiver.check_repeat(event)) return;
		playtrack.pause(1000);
	}, false);

	receiver.source.addEventListener("play", (event) => {
		receiver.log(event);
		if(!receiver.check_repeat(event)) return;
		var track = event['data'] ?? '';
		if(track) {
			playtrack.load(receiver.base_url + track);
		}		
	}, false);
	
	receiver.source.onmessage = (event) => {
		receiver.log(event);
	} 
},

check_repeat: function (event) {
	var val = Number.parseInt(event['lastEventId'] ?? 0);
	var new_event = (val!==receiver.last_id);
	receiver.last_id = val;
	return new_event;	
},

log: function(event) {
	var text = [];
	
	var id = Number.parseInt(event['lastEventId'] ?? 0);
	if(id) text.push(id+'.');
	
	var type = event['type'] ?? null;
	if(type=='message') type=null;
	if(type) text.push('('+type+')');
	
	var data = event['data'] ?? null;
	if(data) {
		data = data.replace(/(\n)+/g, '<br />');
		text.push(data);
	}
	
	text = text.join(' ');
	if(!type) text = '<em>'+text+'</em>';
	if(type=='alert') text = '<strong>'+text+'</strong>';
			
	receiver.messages.append('<li>'+text+'</li>');
	receiver.messages[0].scrollIntoView({ behavior: 'smooth', block: 'end' });
	
	// console.log(event);
	// console.log(text);
},

alert: (message, type="danger") => {
	var text = [
		'<div class="alert alert-'+type+' alert-dismissible" role="alert">',
		'<div>'+message+'</div>',
		'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>',
		'</div>'
	];
	$('#rcvalert').html(text.join(''));
}

};
</script>

<?php $this->endSection();

$this->section('top');
if($action=='view') { ?>
<p class="alert alert-warning">The music receiver is not designed to be used here. Please download it to your device.</p>
<?php }
echo $this->include('player/notfound');
echo $this->include('Htm/Playtrack');
$this->endSection();
