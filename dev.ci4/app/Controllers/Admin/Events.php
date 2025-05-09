<?php namespace App\Controllers\Admin;

class Events extends \App\Controllers\BaseController {
	
private $mdl_events = null;

public function __construct() {
	$this->mdl_events = new \App\Models\Events();
	$this->data['breadcrumbs'][] = 'admin';
	$this->data['breadcrumbs'][] = 'admin/events';
	$this->data['title'] = "events";
	$this->data['heading'] = "Events";
	$this->data['admin'] = \App\Libraries\Auth::check_role('admin');
} 
	
private function find($event_id) {
	$this->data['event'] = $this->mdl_events->withDeleted()->find($event_id);
	if(!$this->data['event']) {
		$message = "Can't find event {$event_id}";
		throw \App\Exceptions\Exception::not_found($message);
	}

	$this->data['states'] = [];
	if(\App\Entities\Clubret::enabled()) $this->data['states'][] = 'clubrets';
	if(\App\Libraries\Track::enabled()) $this->data['states'][] = 'music';
		
	$this->data['id'] = $event_id;
	$this->data['title'] = $this->data['event']->title;
	$this->data['heading'] = $this->data['event']->title;
	if($this->data['event']->private) {
		$this->data['heading'] .= ' (private)';
	}
}
	
public function index() {
	$this->data['body'] = 'admin_events';
	$this->data['base_url'] = site_url('admin/events/view');
	$this->data['events'] = $this->mdl_events->orderBy('date')->withDeleted()->findAll();
	return view('events/index', $this->data);
}

public function add($event_id=0) {
	$event = $event_id ? $this->mdl_events->withDeleted()->find($event_id) : null ;
	if($event) {
		$attribs = $event->toArray();
		$keys = ['id', 'clubrets', 'music', 'deleted_at', 'player', 'dates'];
		foreach($keys as $key) unset($attribs[$key]);
		$this->data['heading'] = $event->title . ' - clone';
	}
	else {
		$attribs = [];
		$this->data['heading'] = 'Create new event';
	}
		
	if($this->request->getPost('save')) {
		// create
		$post_keys = ['title', 'date', 'description'];
		foreach($post_keys as $key) {
			$attribs[$key] = strval($this->request->getPost($key));
		}
		$event = new \App\Entities\Event($attribs);
		$id = $this->mdl_events->insert($event);
		if($id) {
			$this->find($id);
			$this->data['messages'][] = ["Created new event", 'success'];
			return view('events/view', $this->data);
		}
		else {
			$this->data['messages'] = $this->mdl_events->errors();
		}
	}
	
	$this->data['event'] = new \App\Entities\Event($attribs);
	$this->data['breadcrumbs'][] = 'admin/events/add';
	return view('events/add', $this->data);
}

public function view($event_id=0) {
	$this->find($event_id);

	// create entries from returns
	// see also App\Controllers\Admin\Clubrets->event
	if($this->request->getPost('populate')) {
		$mdl_entries = new \App\Models\Entries;
		if($mdl_entries->populate($event_id)) {
			$this->data['messages'][] = ['Club returns added to event entries', 'success'];
		}
		else {
			$this->data['messages'][] = 'Re-population failed';
		}
		// read 
		$this->find($event_id);
	}
	
	$delsure = [
		'title' => "Delete '{$this->data['event']->title}'",
		'message' => "<p>Are you sure you want to delete this event?</p><p class=\" bg-warning-subtle\">All related entries and club returns will also be deleted.</p><p>Ensure the files for event {$event_id} are also deleted.</p>",
	];
	$this->data['delsure'] = new \App\Views\Htm\Delsure($delsure);
	$del_id = $this->data['delsure']->request;
	if($del_id) {
		if($this->mdl_events->delete_all($del_id)) {
			$this->data['messages'][] = ["Event {$del_id} deleted", 'success'];
			$session = \Config\Services::session();
			$session->setFlashdata('messages', $this->data['messages']);
			return redirect()->to(site_url('admin/events'));
		}
		else {
			$this->data['messages'] = $this->mdl_events->errors();
			$this->data['messages'][] = "Event {$del_id} not deleted.";
		}
	}
		
	$state = $this->request->getPost('state');
	switch($state) {
		case 'list':
			$this->data['event']->deleted_at = null;
			$this->mdl_events->save($this->data['event']);
			$this->data['messages'][] = ['Event now listed', 'success'];
			break;
		case 'hide':
			$this->mdl_events->delete($event_id);
			$this->data['messages'][] = ['Event hidden', 'danger'];
			break;
		default:
			$state = '';
	}
	if($state) $this->find($event_id);
		
	$download = $this->request->getGet('dl');
	
	// build entries table
	$tbody = [];
	if($this->data['event']->clubrets==2) { 
		$base_edit = "/admin/entries/edit/{$event_id}";
		$tbody = [];
		foreach($this->data['event']->entries() as $diskey=>$dis) { 
			$cats = [];
			foreach($dis->cats as $cat) {
				$label = $cat->name;
				$count = count($cat->entries);
				if($download!='entries') {
					$params = [
						'disid' => $dis->id,
						'catid' =>$cat->id
					];
					$href = $base_edit .'?' . http_build_query($params);
					$label = anchor($href, $label, ['title' => 'Edit category']);
				}
				$cats[] = [
					'category' => $label, 
					'count' => $count
				];
			}
			$tbody[$diskey] = [
				'disname' => $dis->name,
				'cats' => $cats
			];
		}
	}
	if($download=='entries') {
		$export = ['export' => []];
		foreach($tbody as $dis) {
			$row = ['dis' => $dis['disname']];
			foreach($dis['cats'] as $cat) {
				$export['export'][] = array_merge($row, $cat);
			}
		}
		return $this->export($export, 'entries');
	}
	$this->data['entries'] = $tbody;
			
	// view
	$this->data['back_link'] = "/admin/events";
	$this->data['breadcrumbs'][] = $this->data['event']->breadcrumb(null, 'admin');
	$this->data['clubrets'] = $this->data['event']->clubrets();
	return view('events/admin', $this->data);
}

public function edit($event_id=0) {
	$this->find($event_id);
	
	if($this->request->getPost('save')) {
		// update
		$getPost = $this->request->getPost();
		$getPost['id'] = $event_id;
		// convert form values to arrays
		$getPost['staffcats'] = csv_array($getPost['staffcats']);
		
		// check-boxes
		$getPost['private'] = $getPost['private'] ?? 0 ;
		
		// dates
		$keys = array_keys($this->data['event']->dates);
		$getPost['dates'] = [];
		foreach($keys as $key) {
			$fldname = "dates_{$key}";
			$getPost['dates'][$key] = $getPost[$fldname] ?? null;
		}
		# d($getPost['dates']);
				
		// ToDo - convert discats to be stored as JSON
		$discats = [];
		$formval = filter_json($getPost['discats']);
		foreach($formval as $discat) {
			$discat['name'] = trim($discat['name']);
			$discat['cats'] = trim($discat['cats']);
			if($discat['name'] && $discat['cats']) {
				try {
					$discat['inf'] = parse_ini_string($discat['inf']);
				}
				catch(\Exception $e) {
					$this->data['messages'][] = "Error within Discipline 'inf' section";
				}
				$cats = [];
				$arr = explode("\n", $discat['cats']);
				foreach($arr as $row) {
					$val = csv_array($row);
					if($val) $cats[] = $val;
				}
				$discat['cats'] = $cats;
				
				$discat['opts'] = trim($discat['opts']);
				$discat['opts'] = $discat['opts'] ? explode("\n", $discat['opts']) : [];
		
				$discats[] = $discat;
			}
		}
		$getPost['discats'] = $discats;
		
		$event = new \App\Entities\Event($getPost);
		#d($event->discats);
		
		// delete file
		if($getPost['cmd']=='delfile' && $getPost['key']!=='') {
			$key = intval($getPost['key']);
			$files = $this->data['event']->downloads;
			$list = $files->get();
			$filename = $list[$key] ?? null;
			if($filename) {
				$basename = sprintf('<code>%s</code>', basename($filename));
				if(unlink($filename)) {
					$this->data['messages'][] = ["{$basename} deleted", 'success'];
				} 
				else { 
					$this->data['messages'][] = "Error deleting {$basename}";
				};
			}
		}
		
		// upload file 
		if($getPost['cmd']=='upload') {
			$file = $this->request->getFile('file');
			if($file->isValid()) {
				$filepath = $this->data['event']->filepath('files');
				if($file->move($filepath, $file->getClientName())) {
					$this->data['messages'][] = ["Upload added", 'success'];
				} else {
					$this->data['messages'][] = $file->getErrorString();
				}
			}
			else { 
				$this->data['messages'][] = $file->getErrorString();
			}
		}
		if($this->mdl_events->update($event_id, $event)) {
			$this->data['messages'][] = ["Updated event", 'success'];
		}
		else {
			$this->data['messages'] = $this->mdl_events->errors();
			$this->data['event'] = $event;
		}
		$this->find($event_id);
	}
			
	// view
	$this->data['breadcrumbs'][] = $this->data['event']->breadcrumb(null, 'admin');
	$this->data['breadcrumbs'][] = $this->data['event']->breadcrumb('edit', 'admin');
	return view('events/edit', $this->data);
}

private function export($export, $suffix='') {
	$filetitle = $this->data['event']->title;
	if($suffix) $filetitle .= "_{$suffix}";
	return $this->download($export, 'table', $filetitle);
}
	
}
