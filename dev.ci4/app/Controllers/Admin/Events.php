<?php namespace App\Controllers\Admin;

class Events extends \App\Controllers\BaseController {

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
	if(\App\Libraries\Video::enabled()) $this->data['states'][] = 'videos';
		
	$this->data['id'] = $event_id;
	$this->data['title'] = $this->data['event']->title;
	$this->data['heading'] = $this->data['event']->title;
}
	
public function index() {
	$this->data['body'] = 'admin_events';
	$this->data['base_url'] = site_url('admin/events/view');
	$this->data['events'] = $this->mdl_events->orderBy('date')->withDeleted()->findAll();
	return view('events/index', $this->data);
}

public function add() {
	$event = new \App\Entities\Event();
	foreach(['title', 'date', 'description'] as $key) $event->$key = strval($this->request->getPost($key));
		
	if($this->request->getPost('save')) {
		// create
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
	$this->data['event'] = $event;
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
	
	$cmd = $this->request->getPost('cmd');
	if($cmd=='del_item') {
		$item_id = $this->request->getPost('item_id');
		if($this->mdl_events->delete_all($item_id)) {
			$this->data['messages'][] = ["Event {$item_id} deleted", 'success'];
			$session = \Config\Services::session();
			$session->setFlashdata('messages', $this->data['messages']);
			return redirect()->to(site_url('admin/events'));
		}
		else {
			$this->data['messages'] = $this->mdl_events->errors();
			$this->data['messages'][] = "Event {$item_id} not deleted.";
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
	
	$this->data['modal_delete'] = [
		'title' => "Delete '{$this->data['event']->title}'",
		'description' => "<p>Are you sure you want to delete this event?</p><p class=\"bg-opacity-25 bg-warning\">All related entries and club returns will also be deleted.</p><p>Ensure the files for event {$event_id} are also deleted.</p>",
		'item_id' => $event_id
	];
	
	$download = $this->request->getPost('download');
	
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
		$export = [];
		foreach($tbody as $dis) {
			$row = ['dis' => $dis['disname']];
			foreach($dis['cats'] as $cat) {
				$export[] = array_merge($row, $cat);
			}
		}
		return $this->export($export, 'entries');
	}
	$this->data['entries'] = $tbody;
			
	// view
	$this->data['disk_space'] = $this->data['event']->disk_space();
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
			foreach($this->data['event']->files as $fkey=>$file) {
				if($fkey==$key) {
					$filename = sprintf('<code>%s</code>', $file->getBasename());
					if(unlink($file->getRealPath())) {
						$this->data['messages'][] = ["File {$filename} deleted", 'success'];
					} 
					else { 
						$this->data['messages'][] = "Error deleting file {$filename}";
					};
				}
			}
		}
		
		// upload file 
		if($getPost['cmd']=='upload') {
			$file = $this->request->getFile('file');
			if($file->isValid()) {
				$filepath = $this->data['event']->filepath();
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

private function export($tbody, $suffix='') {
	$filetitle = $this->data['event']->title;
	if($suffix) $filetitle .= "_{$suffix}";
	$data = [
		'export' => $tbody
	];
	return $this->download($data, 'table', $filetitle);
}
	
}
