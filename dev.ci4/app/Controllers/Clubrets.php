<?php namespace App\Controllers;

class Clubrets extends \App\Controllers\BaseController {

function __construct() {
	$this->data['breadcrumbs'][] = 'events';
	$this->model = new \App\Models\Clubrets();
	$this->events = new \App\Models\Events();
	$this->usr_model = new \App\Models\Users();
	$this->data['clubret'] = new \App\Entities\Clubret();
}
	
private function lookup($event_id, $user_id) {
	$this->data['clubret'] = $this->model->lookup($event_id, $user_id);
	if(!$this->data['clubret']) throw new \RuntimeException("Can't find entry {$event_id}/{$user_id}", 404);
	$this->data['user'] = $this->data['clubret']->user();
	$this->data['event'] = $this->data['clubret']->event();
	$this->data['heading'] = sprintf('Return for %s / %s', $this->data['user']->name, $this->data['event']->title);
	$this->data['title'] = $this->data['event']->title;
}
	
public function index() {
	$user_id = session('user_id');
	if(\App\Libraries\Auth::check_role('admin')) {
		$val = intval($this->request->getGet('user'));
		if($val) $user_id = $val;
	}
	
	$this->data['user'] = $this->usr_model->find($user_id);
	if(!$this->data['user']) throw new \RuntimeException("Can't find user $user_id", 404);
			
	$this->data['clubrets'] = $this->model->where('user_id', $user_id)->findAll();
	
	$this->data['breadcrumbs'][] = 'clubrets';
	return view('clubrets/index', $this->data);
}

public function add($event_id=0, $user_id=0) {
	// lookup event
	$this->data['event'] = $this->events->find($event_id);
	if(!$this->data['event']) throw new \RuntimeException("Can't find event $event_id", 404);
	// lookup user
	if(!$user_id) $user_id = session('user_id');
	$this->data['user'] = $this->usr_model->find($user_id);
	if(!$this->data['user']) throw new \RuntimeException("Can't find user $user_id", 404);
	// lookup clubret
	$clubret = $this->model->lookup($event_id, $user_id);
	if($clubret) return $this->edit($event_id, $user_id);
	
	if($this->request->getPost('save')) {
		// get POST
		$getPost = $this->request->getPost();
		foreach(['staff', 'participants'] as $key) {
			$getPost[$key] = empty($getPost[$key]) ? [] : json_decode($getPost[$key], 1);
		}
		$clubret = new \App\Entities\Clubret($getPost);
		$clubret->event_id = $event_id;
		$clubret->user_id = $user_id;
		
		// update
		$id = $this->model->insert($clubret);
		if($id) {
			$this->data['messages'][] = ["Created new entry", 'success'];
			$clubret->id = $id;
		}
		else {
			$this->data['messages'][] = ["Couldn't create new entry", 'danger'];
		}
	}
	else { // create blank clubret
		$clubret = new \App\Entities\Clubret();
		$clubret->event_id = $event_id;
		$clubret->user_id = $user_id;
	}
	// view
	$this->data['breadcrumbs'][] = $this->data['event']->breadcrumb();
	$this->data['breadcrumbs'][] = ["clubrets/add/{$event_id}", 'new'];
	
	$this->data['heading'] = sprintf('New return for %s / %s', $this->data['user']->name, $this->data['event']->title);
	$this->data['clubret'] = $clubret;
	return view('clubrets/edit', $this->data);
}

public function view($event_id=0, $user_id=0) {
	$this->lookup($event_id, $user_id);

	$this->data['clubret']->check();
	
	$this->data['breadcrumbs'][] = $this->data['event']->breadcrumb();
	$this->data['breadcrumbs'][] = $this->data['clubret']->breadcrumb(); 
	
	return view('clubrets/view', $this->data);
}

public function edit($event_id=0, $user_id=0) {
	$this->lookup($event_id, $user_id);
	
	if($this->request->getPost('save')) {
		// get POST
		$getPost = $this->request->getPost(null, FILTER_SANITIZE_STRING);
		$getPost['id'] = $this->data['clubret']->id;
		foreach(['participants', 'staff'] as $key) {
			$val = $this->request->getPost($key);
			$val = empty($val) ? [] : json_decode($val, 1);
			$getPost[$key] = $val;
		}
		$clubret = new \App\Entities\Clubret($getPost);
				
		// filter participants 
		$participants = $clubret->participants;
		$filter = [
			'dis' => FILTER_SANITIZE_STRING,
			'cat' => [
                'filter' => FILTER_SANITIZE_STRING,
                'flags'  => FILTER_FORCE_ARRAY
				],
			'team' => FILTER_SANITIZE_STRING,
			'names' => [
                'filter' => FILTER_SANITIZE_STRING,
                'flags'  => FILTER_FORCE_ARRAY
			],
			'opt' =>FILTER_SANITIZE_STRING
		];
		foreach($participants as $rowkey=>$participant) {
			$participants[$rowkey] = filter_var_array($participant, $filter);
		}

		// sort participants
		$discats = $this->data['event']->discats;
		$sort = [[],[]];
		foreach($participants as $rowkey=>$participant) {
			$sort[0][$rowkey] = $participant['dis'];
			$sort[1][$rowkey] = \App\Entities\Clubret::discat_sort($discats, $participant['dis'], $participant['cat']);
		}
		array_multisort($sort[0], $sort[1], $participants);
		$clubret->participants = $participants;
			
		// filter staff 
		$staff = $clubret->staff; 
		$filter = [
			'cat' => FILTER_SANITIZE_STRING,
			'name' => FILTER_SANITIZE_STRING
		];
		foreach($clubret->staff as $rowkey=>$row) {
			$staff[$rowkey] = filter_var_array($row, $filter);
		}
		// sort staff
		$staffcats = $this->data['event']->staffcats;
		$sort = [[],[]];
		foreach($clubret->staff as $rowkey=>$row) {
			$catkey = array_search($row['cat'], $staffcats);
			$sort[0][$rowkey] = $catkey===false ? 99 : $catkey ;
			$sort[1][$rowkey] = $row['name'];
		}
		array_multisort($sort[0], $sort[1], $staff);
		$clubret->staff = $staff;
		
		// update clubret
		$save = $this->model->save($clubret);
		if($save) { // read
			$this->data['messages'][] = ["Updated entry", 'success'];
			$this->data['clubret'] = $this->model->lookup($event_id, $user_id);
		}
		else {
			$this->data['clubret'] = $clubret;
			$this->data['messages'] = $this->model->errors();
			$this->data['messages'][] = ["Couldn't update entry", 'danger'];
		}

		// update user info
		$user = [];
		foreach($getPost as $key=>$val) {
			if(strpos($key, 'user_')===0) {
				$key = substr($key, 5);
				$user[$key] = trim($val);
			}
		}
		$user = new \App\Entities\User($user);
		$this->usr_model->save($user);
	}
	
	$this->data['heading'] = sprintf('Return for %s / %s', $this->data['user']->name, $this->data['event']->title);
	$this->data['title'] = $this->data['event']->title;
	$this->data['clubret']->check();
		
	// view 
	$this->data['breadcrumbs'][] = $this->data['event']->breadcrumb();
	$this->data['breadcrumbs'][] = $this->data['clubret']->breadcrumb('view'); 
	$this->data['breadcrumbs'][] = $this->data['clubret']->breadcrumb('edit');
	
	if($this->data['event']->clubrets!=1) $this->data['messages'][] = ["Club returns are closed for this event", 'warning'];
	return view('clubrets/edit', $this->data);
}

}
