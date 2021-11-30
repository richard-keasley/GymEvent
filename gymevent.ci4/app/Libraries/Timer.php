<?php namespace App\Libraries;

class Timer {
private $data = []; 

function __construct($length=0, $start=0) {
	// start=0 start new, else continue 
	$now = time();
	if(!$start) $start = $now;
	$this->data = [
		'now' => $now,
		'start' => $start,
		'length' => $length,
		'end' => $start + $length
	];
	$this->data['remainder'] = $this->data['end'] - $now;
}

function __get($prop) {
	return isset($this->data[$prop]) ? $this->data[$prop] : null ;
}

function view($layout='clock') {
	return view("timer/{$layout}", $this->data);
}

}