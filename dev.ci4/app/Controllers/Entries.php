<?php namespace App\Controllers;

class Entries extends \App\Controllers\BaseController {
	
private $model = null;

function __construct() {
	$this->data['breadcrumbs'][] = 'events';
	$this->model = new \App\Models\Entries;
	$this->data['title'] = "entries";
	$this->data['heading'] = "Event entries";
}
	
private function find($event_id) {
	$mod_events = new \App\Models\Events;
	$this->data['event'] = $mod_events->find($event_id);
	if(!$this->data['event']) {
		$message = "Can't find event {$event_id}";
		throw \App\Exceptions\Exception::not_found($message);
	}
	$this->data['entries'] = $this->model->evt_discats($event_id);
	$this->data['heading'] = $this->data['event']->title;
	if($this->data['event']->private) $this->data['heading'] .= ' (private)';
}
	
public function index() {
	$sql = "SELECT DISTINCT `events`.`id` FROM `events` 
	INNER JOIN `evt_disciplines` ON `evt_disciplines`.`event_id`=`events`.`id`
	WHERE `events`.`clubrets`=2
	ORDER BY `events`.`date`";
	$query = $this->model->db->query($sql);
	$ids =  array_column($query->getResultArray(), 'id');
	$mod_events = new \App\Models\Events;
	$this->data['events'] = $mod_events->find($ids);
	
	$this->data['body'] = 'entries';
	$this->data['back_link'] = 'entries';
	$this->data['breadcrumbs'][] = 'entries';
	$this->data['base_url'] = 'entries/view';
	return view('events/index', $this->data);
}

public function view($event_id=0) {
	$this->find($event_id);
	$this->data['heading'] .= ' - entries';
	
	if($this->request->getPost('renumber') && \App\Libraries\Auth::check_path('entries/admin')) {
		$this->model->renumber($event_id);
		$this->data['messages'][] = ['Event renumbered', 'success'];
		$this->find($event_id);
	}
	
	$this->data['back_link'] = $this->data['event']->breadcrumb();
	$this->data['breadcrumbs'][] = $this->data['back_link'];
	$this->data['breadcrumbs'][] = ["entries/view/{$event_id}", 'entries'];
	
	$this->data['users'] = $this->data['event']->users();

	if($this->data['event']->clubrets==0) $this->data['messages'][] = ['Returns have not started for this event', 'warning'];
	if($this->data['event']->clubrets==1) $this->data['messages'][] = ['Returns for this event are still open', 'warning'];
	
	$this->data['format'] = \App\Libraries\Auth::check_role('club') ? 'full' : 'plain ';

	return view('entries/view', $this->data);
}

}
