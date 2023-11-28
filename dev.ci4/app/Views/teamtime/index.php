<?php $this->extend('default');
use \App\Libraries\Teamtime as tt_lib;

$this->section('content'); 



$remote = tt_lib::get_value('settings', 'remote');
if($remote=='send') {
	$runvars = tt_lib::get_value('runvars');

	$error = false;
	if(!$error) {
		$remote_server = tt_lib::get_value('settings', 'remote_server');
		if(!$remote_server) $error = 'No remote server';
	}
	if(!$error) {		
		$client = \Config\Services::curlrequest();
				
		$options = [
			'baseURI' => $remote_server,
			'timeout' => 3,
			'http_errors' => false,
			'debug' => true,
			'json' => $runvars,
			'body' => json_encode($runvars)
		];
		
		$response = $client->post('api/teamtime/remote', $options);
		# d($options);
		# d($client);
		# d($response);
	
		$status = $response->getStatusCode();
		if($status>399) {
			$body = json_decode($response->getBody(), true);
			$error = $body['messages']['error'] ?? 'undefined error';
			$error = "{$status}: {$error}";
			# d($body);
		}
	}
	
	if(!$error) {
		// carry on here
	}
	
	if($error) echo "<p>{$error}</p>";
}





 
$event_id = tt_lib::get_value('settings', 'event_id');
$mdl_events = new \App\Models\Events;
$event = $mdl_events->find($event_id);
$title = $event->title ?? '' ;
echo "<h2>{$title}</h2>";
?>
<p>This app displays the current state of a Team-gym event on many screens across a venue. Messaging, timers and music playback are included.</p>
<?php $this->endSection(); 

$this->section('sidebar'); ?>
<h5>Displays</h5>
<?php
$navbar = new \App\Views\Htm\Navbar(); 
$displays = tt_lib::get_value('displays');
if($displays) {
	$nav = [];
	foreach($displays as $key=>$display) {
		if($key) $nav[] = ["teamtime/display/{$key}", $display['title']];	
	}
	echo $navbar->htm($nav);
}
?>

<h5>Information</h5>
<?php 
$nav = [];
$viewpath = tt_lib::get_viewpath() . '*.php';
foreach(glob($viewpath) as $view) {
	$nav[] = sprintf('teamtime/info/%s', basename($view, '.php'));
}
echo $navbar->htm($nav);

$this->endSection();

$this->section('bottom'); ?>
<div class="toolbar"><?php
echo \App\Libraries\View::back_link('');
echo getlink('control/teamtime', 'TT control');
echo getlink('admin');
?></div>
<?php $this->endSection(); 
