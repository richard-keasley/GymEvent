<?php namespace App\Controllers\Admin;

class Clubrets extends \App\Controllers\BaseController {
	
private $model = null;

function __construct() {
	$this->mdl_clubrets = new \App\Models\Clubrets();
	$this->data['clubret'] = new \App\Entities\Clubret();
	// ToDo: move this to a button
	$tidy = $this->mdl_clubrets->tidy();
	if($tidy) $this->data['messages'][] = ["Tidied $tidy entries", 'warning'];
	$this->data['title'] = "Returns";
	$this->data['heading'] = "Event returns - admin";
	$this->data['breadcrumbs'][] = 'admin';
	$this->data['breadcrumbs'][] = 'admin/events';
}
	
private function lookup($event_id, $user_id) {
	// don't use model->lookup() beacuse we want to include deleted events and users 
	$this->data['clubret'] = $this->mdl_clubrets->where('event_id', $event_id)->where('user_id', $user_id)->first();
	if(!$this->data['clubret']) throw new \RuntimeException("Can't find entry {$event_id}/{$user_id}", 404);
	$this->data['user'] = $this->data['clubret']->user();
	$this->data['event'] = $this->data['clubret']->event();
	$this->data['heading'] = sprintf('Return for %s / %s', $this->data['user']->name, $this->data['event']->title);
	$this->data['title'] = $this->data['event']->title;
}
	
public function index() {
	echo 'Nothing to see here';
}

public function view($event_id=0, $user_id=0) {
	$this->lookup($event_id, $user_id);
	
	if($this->request->getPost('cmd')=='modalUser') {
		$new_user = $this->request->getPost('user_id');
		if($new_user!=$user_id) {
			$clubret = $this->data['clubret'];
			$clubret->user_id = $new_user;
			// update clubret
			$model = new \App\Models\Clubrets;
			if($model->save($clubret)) { 
				// reload 
				$this->data['messages'][] = ["Updated entry", 'success'];
				$session = \Config\Services::session();
				$session->setFlashdata('messages', $this->data['messages']);
				return redirect()->to("admin/clubrets/view/{$event_id}/{$new_user}");
			}
			else {
				$this->data['messages'] = $this->model->errors();
				$this->data['messages'][] = ["Couldn't update entry", 'danger'];
			}
		}
	}

	$this->data['clubret']->check();
	
	// only allow users who do not have returns for this event
	$this->data['users'] = [];
	$user_ids = [];
	$clubrets = $this->data['event']->clubrets();
	foreach($clubrets as $clubret) $user_ids[] = $clubret->user_id;
	$model = new \App\Models\Users;
	$users = $model->orderby('name')->findAll();
	foreach($users as $user) {
		if(!in_array($user->id, $user_ids)) {
			$this->data['users'][] = $user;
		}
	}
		
	$this->data['breadcrumbs'][] = $this->data['event']->breadcrumb(null, 'admin');
	$this->data['breadcrumbs'][] = ["admin/clubrets/event/{$event_id}", 'returns'];
	$this->data['breadcrumbs'][] = $this->data['clubret']->breadcrumb('view', 'admin'); 
	$this->data['back_link'] = "admin/clubrets/event/{$event_id}";
	return view('clubrets/view', $this->data);
}

public function event($event_id=0) {
	$mdl_events = new \App\Models\Events();
	$this->data['event'] = $mdl_events->find($event_id);
	if(!$this->data['event']) throw new \RuntimeException("Can't find event $event_id", 404);
	
	// create entries from returns
	// see also App\Controllers\Admin\Events->event
	if($this->request->getPost('populate')) {
		$mdl_entries = new \App\Models\Entries;
		if($mdl_entries->populate($event_id)) {
			$this->data['messages'][] = ['Club returns added to event entries', 'success'];
		}
		else {
			$this->data['messages'][] = 'Re-population failed';
		}
	}
	
	switch($this->data['event']->clubrets) {
		case 0: 
			$msg = ['Returns for this event are not yet open', 'warning'];
			break;
		case 1:
			$msg = ['Returns for this event are still open', 'success'];
			break;
		case 2:
			$msg = ['Returns for this event are completed', 'warning'];
			break;
		case 3:
			$msg = ['Returns service is closed for this event', 'success'];
			break;
		default:
			$msg = ["Unknown state ({$this->data['event']->clubrets}) for club returns", 'danger'];
	}
	$this->data['back_link'] = "/admin/events/view/{$event_id}";
	$this->data['breadcrumbs'][] = $this->data['event']->breadcrumb(null, 'admin');
	$this->data['breadcrumbs'][] = ["admin/clubrets/event/{$event_id}", 'Returns'];
	$this->data['messages'][] = $msg;
	$this->data['title'] = $this->data['event']->title;
	$this->data['heading'] = $this->data['event']->title . ' - returns';
	$this->data['clubrets'] = $this->data['event']->clubrets();
	
	return view('clubrets/event', $this->data);
}

}
