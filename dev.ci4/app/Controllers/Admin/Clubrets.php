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
	// don't use model->lookup() because we want to include deleted events and users 
	$this->data['clubret'] = $this->model->where('event_id', $event_id)->where('user_id', $user_id)->first();
	if(!$this->data['clubret']) {
		$message = "Can't find entry {$event_id}/{$user_id}";
		\App\Libraries\Exception::not_found($this->request, $message);
	}
	
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
		'description' => '<p>Are you sure you want to delete this return? <span class="bg-opacity-25 bg-warning">This process is irreversible.</span></p>',
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
	if(!$this->data['event']) {
		$message = "Can't find event {$event_id}";
		\App\Libraries\Exception::not_found($this->request, $message);
	}
	
	$download = $this->request->getPost('download');

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
	
	// exclude users who have a return for this event
	$exclude = [0];
	$clubrets = $this->data['event']->clubrets();
	foreach($clubrets as $clubret) $exclude[] = $clubret->user_id;
	$model = new \App\Models\Users;
	$users = $model->orderby('name')->whereNotIn('id', $exclude)->findAll();
	// add new return 
	if($this->request->getPost('cmd')=='modalUser') {
		$user_id = intval($this->request->getPost('user_id'));
		if(in_array($user_id, $exclude)) {
			$newid = 0;
		}
		else {
			$data = [
				'event_id' => $event_id,
				'user_id' => $user_id,
				'staff' => [],
				'participants' => []
			];
			$clubret = new \App\Entities\Clubret($data);
			$newid = $this->model->insert($clubret);
			
		}
		if($newid) {
			$this->data['messages'][] = ["Created new entry", 'success'];
			$clubret->id = $newid;
			$this->data['event'] = $mdl_events->find($event_id);			
		}
		else {
			$this->data['messages'][] = ["Couldn't create new entry", 'danger'];
		}
	}
	// add users dialogue
	$this->data['users_dialogue'] = [
		'title' => 'Add a club return to this event',
		'user_id' => 0,
		'users' => $users,
		'description' => sprintf('Add a new club return to <em>%s</em>.', $this->data['event']->title)
	];
	
	// build summary table
	$fees = []; $cols = []; $count = []; $tbody = [];

	foreach($clubrets as $rowkey=>$clubret) {
		$user = $clubret->user();
		if($download=='summary') {
			$label = $user ? $user->name : '[unknown]' ;
		}
		else {
			if($user) {
				$label = $user->name;
				if($user->deleted_at) $label .= ' <i class="bi bi-x-circle text-danger" title="This user is disabled"></i>';		
			}
			else $label = 'unknown <i class="bi bi-exclamation-triangle-fill text-warning"></i>';
			
			$ok = $clubret->check();
			if(!$ok) $label .= ' <span class="bi bi-exclamation-triangle-fill text-warning" title="There are errors in this return"></span>';
			$label = getlink($clubret->url('view', 'admin'), $label);
			if($user) $label .= ' ' . $user->link();
		}
		$tbody[$rowkey] = [
			'club' => $label,
			'updated' => $clubret->updated
		];
				
		$count[$rowkey] = [];
		foreach($clubret->participants as $participant) {
			$dis = $participant['dis'];
			if(empty($count[$rowkey][$dis])) $count[$rowkey][$dis] = 0;
			$count[$rowkey][$dis]++;
			if(!in_array($dis, $cols)) $cols[] = $dis;
		}
			
		$cr_fees = $clubret->fees('fees');
		$fees[$rowkey] = array_sum(array_column($cr_fees, 1));
	}

	foreach($tbody as $rowkey=>$row) {
		foreach($cols as $colkey) {
			$val = $count[$rowkey][$colkey] ?? 0;
			$tbody[$rowkey][$colkey] = $val;
		}
		$tbody[$rowkey]['fees'] = $fees[$rowkey];
	}
	if($download=='summary') return $this->export($tbody, 'summary');
	$this->data['summary'] = $tbody;
	
	// build staff table
	$tbody = [];
	foreach($this->data['event']->staff() as $entkey=>$entry) {
		$tbody[] = [
			# $entkey + 1,
			$entry['club'],
			humanize($entry['cat']),
			$entry['name'],
			$entry['bg'],
			# date('d-M-Y', $entry['dob'])
		];
	}
	if($download=='staff') return $this->export($tbody, 'staff');
	$this->data['staff'] = $tbody;
	
	// build participants table
	$tbody = [];
	foreach($this->data['event']->participants() as $dis) { 
		foreach($dis['cats'] as $cat) { 	 
			foreach($cat['entries'] as $entkey=>$entry) {
				if(!$entry['club']) $entry['club'] = 'unknown';
				$row = [
					'dis' => $dis['name'],
					'cat' => $cat['name'],
					'club' => $entry['club'],
					'name' => $entry['name'],
					'DoB' => date('d-M-Y', $entry['dob'])
				];
				if(!$entkey) $has_opt = $entry['opt'];
				if($has_opt) $row['opt'] = humanize($entry['opt']);
				$tbody[] = $row;
			}
		}
	}
	if($download=='participants') return $this->export($tbody, 'participants');
	$this->data['participants'] = $tbody;

	// view
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

public function names($event_id=0) {
	$mdl_events = new \App\Models\Events();
	$this->data['event'] = $mdl_events->find($event_id);
	if(!$this->data['event']) {
		$message = "Can't find event {$event_id}";
		\App\Libraries\Exception::not_found($this->request, $message);
	}
	
	$download = $this->request->getPost('download');
	
	// build participants table
	$mdl_users = new \App\Models\Users();
	$tbody = [];
	foreach($this->data['event']->clubrets() as $clubret) {
		$user = $mdl_users->withDeleted()->find($clubret->user_id);
		foreach($clubret->participants as $rowkey=>$row) {
			foreach($row['names'] as $key=>$name) {
				$namestring = new \App\Entities\namestring($name);
				$tbody[] = [
					'club' => $user ? $user->abbr : '?',
					'row' => $rowkey + 1,
					'dis' => $row['dis'],
					'cat' => implode(' ', $row['cat']),
					'name' => $namestring->name,
					'BG' => $namestring->bg,
					'DoB' => $namestring->htm_dob()
				];
			}
		}
	}
	if($download=='names') return $this->export($tbody, 'names');
	$this->data['names'] = $tbody;
	
	// view
	$this->data['back_link'] = "/admin/clubrets/event/{$event_id}";
	$this->data['breadcrumbs'][] = $this->data['event']->breadcrumb(null, 'admin');
	$this->data['breadcrumbs'][] = ["admin/clubrets/event/{$event_id}", 'Returns'];
	$this->data['breadcrumbs'][] = ["admin/clubrets/names/{$event_id}", 'Name check'];
	$this->data['title'] = $this->data['event']->title;
	$this->data['heading'] = $this->data['event']->title . ' - name check';
	
	return view('clubrets/names', $this->data);
}

private function export($tbody, $suffix='') {
	$filetitle = $this->data['event']->title;
	if($suffix) $filetitle .= "_{$suffix}";
	$data = [
		'export' => $tbody
	];
	return $this->download($data, 'table', $filetitle);
}

}
