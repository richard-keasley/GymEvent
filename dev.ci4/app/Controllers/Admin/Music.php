<?php namespace App\Controllers\Admin;

class Music extends \App\Controllers\BaseController {
	
private $mdl_events = null;
private $mdl_entries = null;

private function find($event_id) {
	$this->mdl_events = new \App\Models\Events;
	$this->mdl_entries = new \App\Models\Entries;
	$this->data['event'] = $this->mdl_events->find($event_id);
	if(!$this->data['event']) throw new \RuntimeException("Can't find event $event_id", 404);
	$this->data['breadcrumbs'][] = $this->data['event']->breadcrumb(null, 'admin');
	$this->data['breadcrumbs'][] = ["admin/music/view/{$event_id}", 'music'];
}

function __construct() {
	$this->data['breadcrumbs'][] = 'admin';
	$this->data['breadcrumbs'][] = 'admin/events';
}
	
public function index() {
	$this->mdl_events = new \App\Models\Events();
	$this->data['events'] = $this->mdl_events->whereIn('music', [1, 2])->orderBy('date')->findAll();

	$this->data['breadcrumbs'][] = 'admin/music';
	$this->data['base_url'] = 'admin/music/view';
	$this->data['body'] = 'admin_music';
	return view('events/index', $this->data);
}

public function view($event_id=0) {
	$this->find($event_id);
	
	# d($this->request->getPost());

	if($this->request->getPost('set_state')) {
		$data = [
			'id' => $event_id,
			'music' => intval($this->request->getPost('music'))
		];
		# d($data);
		$this->mdl_events->save($data);
		$this->data['event'] = $this->mdl_events->find($event_id);
	}
	
	if($this->request->getPost('cmd')=='update') {
		$getPost = $this->request->getPost();
		$post_val = $getPost['val'] ?? 0 ;
		$entries = $this->data['event']->entries();
		$track = new \App\Libraries\Track;
		$track->event_id = $this->data['event']->id;
		foreach($entries as $dis) { 
			foreach($dis->cats as $cat) {
				foreach($cat->entries as $entry) {
					$track->entry_num = $entry->num;
					$ent_music = $entry->music;
					foreach($ent_music as $exe=>$check_state) {
						$track->exe = $exe;
						$search = "trk_{$track->filebase()}";
						if(isset($getPost[$search])) {
							$ent_music[$exe] = $post_val;
						}
					}
					$entry->music = $ent_music;
					if($entry->hasChanged('music')) {
						$entry->updateMusic();
					}
				}
			}
		}
	}
				
	$this->data['filter'] = []; 
	$flds = ['dis', 'cat', 'user', 'status'];
	foreach($flds as $fld) $this->data['filter'][$fld] = $this->request->getGet($fld);
		
	$this->data['users'] = $this->mdl_entries->evt_users($event_id);
	$this->data['entries'] = $this->data['event']->entries();
	$this->data['title'] = $this->data['event']->title;
	$this->data['heading'] = $this->data['event']->title;
	return view('music/admin', $this->data);
}

public function clubs($event_id=0) {
	$this->find($event_id);
	
	$this->data['breadcrumbs'][] = ["admin/music/clubs/{$event_id}", 'clubs'];
	
	$status = $this->request->getGet('status');
	if(!isset(\App\Libraries\Track::state_labels[$status])) $status = 0;
	$this->data['state_labels'] = $status ? [$status] : \App\Libraries\Track::state_labels;
	$this->data['status'] = $status;
		
	$this->data['users'] = $this->mdl_entries->evt_users($event_id);
	$this->data['entries'] = $this->data['event']->entries();
	$this->data['title'] = $this->data['event']->title;
	$this->data['heading'] = $this->data['event']->title;
	
	return view('music/clubs', $this->data);

	
}

}
