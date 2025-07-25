<?php namespace App\Controllers\Admin;

class Clubrets extends \App\Controllers\BaseController {
	
function __construct() {
	$this->data['clubret'] = new \App\Entities\Clubret();
	// ToDo: move this to a button
	$tidy = model('Clubrets')->tidy();
	if($tidy) $this->data['messages'][] = ["Tidied {$tidy} entries", 'warning'];
	$this->data['title'] = "Returns";
	$this->data['heading'] = "Event returns - admin";
	$this->data['breadcrumbs'][] = 'admin';
	$this->data['breadcrumbs'][] = 'admin/events';
}
	
private function lookup($event_id, $user_id) {
	// don't use model->lookup() because we want to include deleted events and users 
	$this->data['clubret'] = model('Clubrets')->where('event_id', $event_id)->where('user_id', $user_id)->first();
	if(!$this->data['clubret']) {
		$message = "Can't find entry {$event_id}/{$user_id}";
		throw \App\Exceptions\Exception::not_found($message);
	}
	
	$this->data['user'] = $this->data['clubret']->user;
	$this->data['event'] = $this->data['clubret']->event;
	$this->data['heading'] = sprintf('Return for %s / %s', $this->data['user']->name, $this->data['event']->title);
	$this->data['title'] = $this->data['event']->title;
}
	
public function index() {
	return view('admin/index', $this->data);
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
			if(model('Clubrets')->save($clubret)) { 
				// reload 
				$this->data['messages'][] = ["Updated return", 'success'];
				$session = \Config\Services::session();
				$session->setFlashdata('messages', $this->data['messages']);
				return redirect()->to("admin/clubrets/view/{$event_id}/{$new_user}");
			}
			else {
				$this->data['messages'] = model('Clubrets')->errors();
				$this->data['messages'][] = ["Couldn't update return", 'danger'];
			}
		}
	}
	
	$delsure = [
		'title' => 'Delete this return?',
		'message' => '<p>Are you sure you want to delete this return? <span class="bg-warning-subtle">This process is irreversible.</span></p>',
	];
	$this->data['delsure'] = new \App\Views\Htm\Delsure($delsure);
	$del_id = $this->data['delsure']->request;
	if($del_id) {
		if(model('Clubrets')->delete($del_id)) {
			$this->data['messages'][] = ["Return deleted", 'success'];
			$session = \Config\Services::session();
			$session->setFlashdata('messages', $this->data['messages']);
			return redirect()->to($back_link);
		}
		else {
			$this->data['messages'] = model('Clubrets')->errors();
		}	
	}
	
	$this->data['clubret']->check();
	
	// only allow users who do not have returns for this event
	$exclude = [];
	$clubrets = $this->data['event']->clubrets();
	foreach($clubrets as $clubret) $exclude[] = $clubret->user_id;
	$this->data['users_dialogue'] = [
		'title' => 'Change user for this return',
		'user_id' => $this->data['user']->user_id,
		'users' => model('Users')->orderby('name')->whereNotIn('id', $exclude)->findAll(),
		'description' => sprintf('Move this return from <em>%s</em> to selected user.', $this->data['user']->name)
	];
			
	$this->data['breadcrumbs'][] = $this->data['event']->breadcrumb(null, 'admin');
	$this->data['breadcrumbs'][] = ["admin/clubrets/event/{$event_id}", 'returns'];
	$this->data['breadcrumbs'][] = $this->data['clubret']->breadcrumb('view', 'admin'); 
	$this->data['back_link'] = $back_link;
	return view('clubrets/view', $this->data);
}

public function event($event_id=0) {
	$this->data['event'] = model('Events')->find($event_id);
	if(!$this->data['event']) {
		$message = "Can't find event {$event_id}";
		throw \App\Exceptions\Exception::not_found($message);
	}
	
	$download = $this->request->getGet('dl');
	$clubrets = $this->data['event']->clubrets();
	
	// create entries from returns
	// see also App\Controllers\Admin\Events->event
	if($this->request->getPost('populate')) {
		if(model('Entries')->populate($event_id)) {
			$this->data['messages'][] = ['Club returns added to event entries', 'success'];
		}
		else {
			$this->data['messages'][] = 'Re-population failed';
		}
	}
	
	// exclude users who have a return for this event
	$exclude = [0];
	foreach($clubrets as $clubret) $exclude[] = $clubret->user_id;
	$users = model('Users')->orderby('name')->whereNotIn('id', $exclude)->findAll();
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
			$newid = model('Clubrets')->insert($clubret);
		}
		if($newid) {
			$this->data['messages'][] = ["Created new entry", 'success'];
			// reload event
			$this->data['event'] = model('Events')->find($event_id);
			$clubrets = $this->data['event']->clubrets();
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
		$user = $clubret->user;
		if($download=='summary') {
			$club = $user ? $user->name : '[unknown]' ;
		}
		else {
			if($user) {
				$club = $user->name;
				if($user->deleted_at) $club .= ' <i class="bi bi-x-circle text-danger" title="This user is disabled"></i>';		
			}
			else $club = 'unknown <i class="bi bi-exclamation-triangle-fill text-warning"></i>';
			
			$ok = $clubret->check();
			if(!$ok) $club .= ' <span class="bi bi-exclamation-triangle-fill text-warning" title="There are errors in this return"></span>';
			
			$club = getlink($clubret->url('view', 'admin'), $club);
			
			if($user) $club .= ' ' . $user->link();
		}
		
		$tbody[$rowkey] = [
			'club' => $club,
			'updated' => $clubret->updated
		];
		
		if($this->data['event']->stafffee) {
			$val = $clubret->stafffee ? 'X' : '' ;
			if($download!=='summary') {
				$val = $val ?
					'<span class="bi-check text-success"></span>' : 
					'<span class="bi-x text-danger"></span>' ;
			}
			$tbody[$rowkey]['staff'] = $val;
		}

		if($this->data['event']->terms) {
			$val = $clubret->terms ? 'X' : '' ;
			if($download!=='summary') {
				$val = $val ?
					'<span class="bi-check text-success"></span>' : 
					'<span class="bi-x text-danger"></span>' ;
			}
			$tbody[$rowkey]['terms'] = $val;
		}
						
		$count[$rowkey] = [];
		foreach($clubret->participants as $participant) {
			$dis = $participant['dis'];
			if(empty($count[$rowkey][$dis])) $count[$rowkey][$dis] = 0;
			$count[$rowkey][$dis]++;
			if(!in_array($dis, $cols)) $cols[] = $dis;
		}
			
		$cr_fees = $clubret->fees;
		$fees[$rowkey] = array_sum(array_column($cr_fees, 1));
	}

	foreach($tbody as $rowkey=>$row) {
		foreach($cols as $colkey) {
			$val = $count[$rowkey][$colkey] ?? 0;
			$tbody[$rowkey][$colkey] = $val;
		}
		$tbody[$rowkey]['fees'] = $fees[$rowkey];
	}
	if($download=='summary') {
		return $this->export($tbody, 'summary');
	}
	$this->data['summary'] = $tbody;
	
	// build staff table
	$tbody = [];
	foreach($this->data['event']->staff() as $entkey=>$entry) {
		$tbody[] = [
			# '#' => $entkey + 1,
			'club' => $entry['club'],
			'role' => humanize($entry['cat']),
			'name' => $entry['name'],
			# 'BG' => $entry['bg'],
			# 'DoB' => date('d-M-Y', $entry['dob'])
		];
	}
	if($download=='staff') {
		return $this->export($tbody, 'staff');
	}
	$this->data['staff'] = $tbody;
	
	// build participants table
	$tbody = [];
	foreach($this->data['event']->participants() as $dis) { 
		foreach($dis['cats'] as $cat) { 	 
			foreach($cat['entries'] as $entkey=>$entry) {
				if(!$entry['club']) $entry['club'] = 'unknown';
				$row = [
					'dis' => $dis['name'],
					'cat' => humanize($cat['name']),
					'club' => $entry['club'],
					'name' => $entry['name'],
					'DoB' => $entry['dob'],
				];
				if(!$entkey) $has_opt = $entry['opt'];
				if($has_opt) $row['opt'] = humanize($entry['opt']);
				$tbody[] = $row;
			}
		}
	}
	if($download=='participants') {
		return $this->export($tbody, 'participants');
	}
	$this->data['participants'] = $tbody;

	// view
	$this->data['messages'][] = match($this->data['event']->clubrets) {
		0 => ['Returns for this event are not yet open', 'warning'],
		1 => ['Returns for this event are still open', 'success'],
		2 => ['Returns for this event are completed', 'warning'],
		3 => ['Returns service is closed for this event', 'success'],
		default => ["Unknown state ({$this->data['event']->clubrets}) for club returns", 'danger']
	};
		
	$this->data['back_link'] = "/admin/events/view/{$event_id}";
	$this->data['breadcrumbs'][] = $this->data['event']->breadcrumb(null, 'admin');
	$this->data['breadcrumbs'][] = ["admin/clubrets/event/{$event_id}", 'Returns'];
	$this->data['title'] = $this->data['event']->title;
	$this->data['heading'] = $this->data['event']->title . ' - returns';
	$this->data['clubrets'] = $this->data['event']->clubrets();
	
	return view('clubrets/event', $this->data);
}

public function names($event_id=0) {
	$this->data['event'] = model('Events')->find($event_id);
	if(!$this->data['event']) {
		$message = "Can't find event {$event_id}";
		throw \App\Exceptions\Exception::not_found($message);
	}
		
	// build participants table
	$users = [];
	$tbody = [];
	foreach($this->data['event']->clubrets() as $clubret) {
		$user = model('Users')->withDeleted()->find($clubret->user_id);
		$club = $user->abbr ?? '?';
				
		foreach($clubret->participants as $row) {
			foreach($row['names'] as $name) {
				$tr = [
					'club' => $club,
					'dis' => $row['dis'],
					'cat' => humanize(implode(' ', $row['cat'])),
				];				
				$namestring = new \App\Libraries\Namestring($name);
				foreach($namestring->__toArray() as $key=>$val) {
					$tr[$key] = $val;
				}
				$tbody[] = $tr;
			}
		}
		foreach($clubret->staff as $row) {
			$tr = [
				'club' => $club,
				'dis' => 'staff',
				'cat' => humanize($row['cat']),
			];	
			$namestring = new \App\Libraries\Namestring($row['name']);
			foreach($namestring->__toArray() as $key=>$val) {
				$tr[$key] = $val;
			}
			$tbody[] = $tr;
		}
	}
	
	$download = $this->request->getPost('download');
	if($download=='names') {
		return $this->export($tbody, 'names');
	}
	
	// view
	$this->data['names'] = $tbody;
	$this->data['back_link'] = "/admin/clubrets/event/{$event_id}";
	$this->data['breadcrumbs'][] = $this->data['event']->breadcrumb(null, 'admin');
	$this->data['breadcrumbs'][] = ["admin/clubrets/event/{$event_id}", 'Returns'];
	$this->data['breadcrumbs'][] = ["admin/clubrets/names/{$event_id}", 'Name check'];
	$this->data['title'] = $this->data['event']->title;
	$this->data['heading'] = $this->data['event']->title . ' - name check';
	
	return view('clubrets/names', $this->data);
}

private function export($export, $suffix='') {
	$filename = $this->data['event']->title;
	if($suffix) $filename .= "_{$suffix}";
	$filename .= '.csv';
	return $this->download($filename, $export);
}

}
