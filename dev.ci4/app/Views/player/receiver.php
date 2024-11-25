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



<?php $this->endSection();

$this->section('top');
if($action=='view') { ?>
<p class="alert alert-warning">The music receiver is not designed to be used here. Please download it to your device.</p>
<?php }
echo $this->include('player/notfound');
echo $this->include('Htm/Playtrack');
$this->endSection();
