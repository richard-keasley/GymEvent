<?php namespace App\Controllers\Control;

class Events extends \App\Controllers\BaseController {

function __construct() {
	$this->data['breadcrumbs'][] = "events";
	$this->mdl_events = new \App\Models\Events;
}

private function find($event_id) {
	$event = $this->mdl_events->find($event_id);
	if(!$event) {
		$message = "Can't find event {$event_id}";
		throw \App\Exceptions\Exception::not_found($message);
	}
	$this->data['title'] = $event->title;
	$this->data['heading'] = $event->title;
	if($event->private) {
		$this->data['heading'] .= ' (private)';
	}
	return $event;
}

public function index() {
	return view('index', $this->data);
}

public function view($event_id=0) {
	$this->data['event'] = $this->find($event_id);
	return view('events/control', $this->data);
}

}
