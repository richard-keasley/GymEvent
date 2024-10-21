<?php 
namespace App\Libraries\Sse\channel;

class none {

public $name = 'none';

function read() {
	$arr = [
		'comment' => "No channel",
		'retry' => 60
	];
	return new \App\Libraries\Sse\Event($arr);
}

function write($event) {
	return false;
}	
	
}