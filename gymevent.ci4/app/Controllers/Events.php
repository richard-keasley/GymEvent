<?php namespace App\Controllers;

class Events extends \App\Controllers\BaseController {
	
private $model = null;

public function __construct() {
	$this->data['breadcrumbs'][] = 'events';
	$this->model = new \App\Models\Events();
}
	
public function index() {
$this->data['body'] = <<< EOT
<p>Select the event you are interested in.</p>
EOT;
	$this->data['base_url'] = base_url('events/view');
	$this->data['events'] = $this->model->orderBy('date')->findAll();
	return view('events/index', $this->data);
}

public function view($event_id=0) {
	$this->data['event'] = $this->model->find($event_id);
	if(!$this->data['event']) throw new \RuntimeException("Can't find event $event_id", 404);
		
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

// end
}
