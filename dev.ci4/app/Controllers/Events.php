<?php namespace App\Controllers;

class Events extends \App\Controllers\BaseController {
	
private $model = null;

public function __construct() {
	$this->data['breadcrumbs'][] = 'events';
	$this->model = new \App\Models\Events();
}
	
public function index() {
	$this->data['options'] = ['past', 'current', 'future'];

	$option = $this->request->getGet('f');
	if(!in_array($option, $this->data['options'])) $option = 'current'; 
	$clubrets = match($option) {
		'past' => [3],
		'future' => [0],
		default => [1, 2]
	};
	$this->data['option'] = $option;

	$this->data['events'] = $this->model->whereIn('clubrets', $clubrets)->orderBy('date')->findAll();

	$this->data['body'] = 'events';
	$this->data['base_url'] = base_url('events/view');
	return view('events/index', $this->data);
}

public function view($event_id=0) {
	if(!$event_id) return $this->index();
	$this->data['event'] = $this->model->find($event_id);
 	if(!$this->data['event']) throw new \RuntimeException("Can't find event {$event_id}", 404);
    
	// view
	$this->data['id'] = $event_id;
	$this->data['title'] = $this->data['event']->title;
	$this->data['heading'] = $this->data['event']->title;
	$this->data['state_labels'] = [];
	$this->data['back_link'] = 'events';
	$this->data['breadcrumbs'][] = $this->data['event']->breadcrumb();
	$this->data['clubrets'] = $this->data['event']->clubrets();
	$this->data['entries'] = $this->data['event']->entries();
	$this->data['admin'] = 0;#\App\Libraries\Auth::check_role('admin');
	return view('events/view', $this->data);
}

}
