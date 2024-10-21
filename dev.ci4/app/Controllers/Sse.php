<?php namespace App\Controllers;

class Sse extends \App\Controllers\BaseController {
	
public function index() {
	return view('index', $this->data);
}

public function receive($channel='', $id=0, $delay=0) {
	$stream = new \App\Libraries\Sse\Stream($channel, $id);
		
	$event = $stream->channel->read();
	echo "<pre>{$event}</pre>";
	
	$arr = [
		'id' => $event->id + 1,
		'type' => 'play',
		'data' => "091_FLR.mp3"
	];
	if($arr['id'] > 999) $arr['id'] = 1;
	$event = new \App\Libraries\Sse\Event($arr);
	$success = $stream->channel->write($event);
	# echo $success ? 'saved' : 'failed' ;
	$event = $stream->channel->read();
	echo "<pre>{$event}</pre>";
}

}
