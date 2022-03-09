<?php namespace App\Controllers\Admin;

class Clubrets extends \App\Controllers\BaseController {
	
private $model = null;

function __construct() {
	$this->model = new \App\Models\Clubrets;
	$this->data['clubret'] = new \App\Entities\Clubret();
	// ToDo: move this to a button
	$tidy = $this->model->tidy();
	if($tidy) $this->data['messages'][] = ["Tidied $tidy entries", 'warning'];
	$this->data['title'] = "Returns";
	$this->data['heading'] = "Event returns - admin";
	$this->data['breadcrumbs'][] = 'admin';
	$this->data['breadcrumbs'][] = 'admin/events';
}
	
private function lookup($event_id, $user_id) {
	// don't use model->lookup() beacuse we want to include deleted events and users 
	$this->data['clubret'] = $this->model->where('event_id', $event_id)->where('user_id', $user_id)->first();
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
	$back_link = "admin/clubrets/event/{$event_id}";
	
	if($this->request->getPost('cmd')=='modalUser') {
		$new_user = $this->request->getPost('user_id');
		if($new_user!=$user_id) {
			$clubret = $this->data['clubret'];
			$clubret->user_id = $new_user;
			// update clubret
			if($this->model->save($clubret)) { 
				// reload 
				$this->data['messages'][] = ["Updated return", 'success'];
				$session = \Config\Services::session();
				$session->setFlashdata('messages', $this->data['messages']);
				return redirect()->to("admin/clubrets/view/{$event_id}/{$new_user}");
			}
			else {
				$this->data['messages'] = $this->model->errors();
				$this->data['messages'][] = ["Couldn't update return", 'danger'];
			}
		}
	}

	if($this->request->getPost('cmd')=='del_item') {
		$item_id = $this->request->getPost('item_id');
		if($this->model->delete($item_id)) {
			$this->data['messages'][] = ["Return deleted", 'success'];
			$session = \Config\Services::session();
			$session->setFlashdata('messages', $this->data['messages']);
			return redirect()->to($back_link);
		}
		else {
			$this->data['messages'] = $this->model->errors();
		}
	}
	$this->data['modal_delete'] = [
		'title' => 'Delete this return',
		'description' => '<p>Are you sure you want to delete this return? <span class="alert-warning">This process is irreversible.</span></p>',
		'cmd' => "del_item",
		'item_id' => $this->data['clubret']->id
	];

	$this->data['clubret']->check();
	
	// only allow users who do not have returns for this event
	$exclude = [];
	$clubrets = $this->data['event']->clubrets();
	foreach($clubrets as $clubret) $exclude[] = $clubret->user_id;
	$model = new \App\Models\Users;
	$this->data['users_dialogue'] = [
		'title' => 'Change user for this return',
		'user_id' => $this->data['user']->user_id,
		'users' => $model->orderby('name')->whereNotIn('id', $exclude)->findAll(),
		'description' => sprintf('Move this return from <em>%s</em> to selected user.', $this->data['user']->name)
	];
			
	$this->data['breadcrumbs'][] = $this->data['event']->breadcrumb(null, 'admin');
	$this->data['breadcrumbs'][] = ["admin/clubrets/event/{$event_id}", 'returns'];
	$this->data['breadcrumbs'][] = $this->data['clubret']->breadcrumb('view', 'admin'); 
	$this->data['back_link'] = $back_link;
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
