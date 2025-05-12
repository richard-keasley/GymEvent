<?php namespace App\Controllers\Control;

class Player extends \App\Controllers\BaseController {

private $mdl_events = null;
	
function __construct() {
	$this->data['breadcrumbs'][] = "events";
	$this->mdl_events = new \App\Models\Events;
}

public function index() {
	$this->data['title'] = 'Music player';
	$this->data['heading'] = 'Music player';
	$this->data['events'] = $this->mdl_events->where('music', 2)->findAll();
	$this->data['breadcrumbs'][] = "control/player";
	$this->data['base_url'] = 'control/player/view';
	$this->data['body'] = 'control_player';
	return view('events/index', $this->data);
}

public function view($event_id=0, $action='view') {
	$tt_event = \App\Libraries\Teamtime::get_value('settings', 'event_id');
	if($tt_event==$event_id) {
		return redirect()->to('control/teamtime/player');
	}
		
	$this->data['event'] = $this->find($event_id);
	$this->data['title'] = 'Music player';
	$this->data['heading'] = $this->data['event']->title;
		
	switch($action) {
		case 'save':
		$this->data['breadcrumbs'] = null;
		$this->data['showhelp'] = false;
		break;
		
		default:
		$action = 'view';
		$this->data['breadcrumbs'][] = $this->data['event']->breadcrumb();
		$this->data['breadcrumbs'][] = ["control/player/view/{$event_id}", 'player'];
	}
	$this->data['action'] = $action;
	
	// get view
	$page = view("player/view", $this->data);
	
	return ($action=='save') ?
		$this->saveplayer($event_id, 'player.htm', $page) :
		$page ;
}

public function receiver($event_id=0, $action='view') {
	$this->data['event'] = $this->find($event_id);
	$this->data['title'] = 'Music receiver';
	$this->data['heading'] = $this->data['event']->title;
	
	$stream = new \App\Libraries\Sse\Stream('music');
	$this->data['source_url'] = $stream->url();
	
	switch($action) {
		case 'save':
		$this->data['breadcrumbs'] = null;
		$this->data['music_path'] = "music/";
		break;
		
		default:
		$action = 'view';
		$this->data['breadcrumbs'][] = ["control/player/view/{$event_id}", 'player'];
		$this->data['breadcrumbs'][] = ["control/player/receiver/{$event_id}", 'receiver'];
		$this->data['music_path'] = site_url("public/events/{$event_id}/music/");
	}
	$this->data['action'] = $action;
	
	// get view
	$page = view("player/receiver", $this->data);

	return ($action=='save') ?
		$this->saveplayer($event_id, 'receiver.htm', $page) :
		$page ;
}

public function edit($event_id=0) {
	$event = $this->find($event_id);
	$cmd = $this->request->getPost('cmd');
	
	if($cmd=='update') { 
		//save player
		$player = filter_json($this->request->getPost('player'), 0);
		foreach($player as $round_id=>$round) {
			$nums = [];
			$val = $round->entry_nums ?? '';
			foreach(preg_split('/[^\d]+/', $val) as $num) {
				if($num) $nums[] = $num;
			}
			$player[$round_id]->entry_nums = $nums;
		}
		$event->player = $player;
		
		if($event->hasChanged()) {
			$this->data['messages'][] = ["Player info saved", 'success'];
			$this->mdl_events->save($event);
			$event = $this->mdl_events->find($event_id);
		}
	}	
	
	// all tracks needed for this event
	$entries = [];
	
	$scoreboard = new \App\ThirdParty\scoreboard;
	$exeset_names = [];
	foreach($scoreboard->get_exesets() as $exeset) {
		$exeset_names[$exeset['SetId']] = $exeset['Name'];
	}
	
	foreach($event->entries() as $dis_key=>$dis) {
		foreach($dis->cats as $cat_key=>$cat) {
			$cat_entry = [
				'dis' => $dis->abbr,
				'cat' => $cat->abbr,
				'exeset' => $exeset_names[$cat->exercises] ?? '', 
			];
			
			foreach($cat->music as $exe) {
				$cat_entry['exe'] = $exe;
				$cat_entry['entries'] = [];
				foreach($cat->entries as $entry) {
					$ent_group = $entry->get_rundata('group');
					$cat_entry['entries'][] = [
						'num' => $entry->num,
						'group' => $entry->get_rundata('group'),
						'order' => $entry->get_rundata('order')
					];
				}
				$entries[] = $cat_entry;
			}
		}	
	}
	$this->data['entries'] = $entries;
	
	if($cmd=='rebuild') {
		$player = []; $sort_arr = [];
		foreach($entries as $cat) {
			$cat_description = "{$cat['dis']}:{$cat['cat']}";
			foreach($cat['entries'] as $cat_entry) {
				$key = array_search($cat_entry['order'], $sort_arr);
				if($key===false) {
					$key = count($sort_arr);
					$sort_arr[] = $cat_entry['order'];
					$exe = strtoupper($cat['exe']);
					$player[] = [
						'exe' => $exe,
						'title' => "{$cat_entry['group']} {$cat['exeset']}",
						'description' => [],
						'entry_nums' => []
					];
				}
				if(!in_array($cat_description, $player[$key]['description'])) {
					$player[$key]['description'][] = $cat_description;
				}
				$player[$key]['entry_nums'][] = $cat_entry['num'];
			}
		}
		foreach($player as $key=>$row) {
			$player[$key]['description'] = implode(", ", $row['description']);
		}
		array_multisort($sort_arr, $player);
		
		$event->player = $player;
		if($event->hasChanged()) {
			$this->data['messages'][] = ["Player info re-built", 'success'];
			$this->mdl_events->save($event);
			$event = $this->mdl_events->find($event_id);
		}
	}
	
	$this->data['event'] = $event;
	$this->data['breadcrumbs'][] = $this->data['event']->breadcrumb();
	$this->data['breadcrumbs'][] = ["control/player/view/{$event_id}", 'player'];
	$this->data['breadcrumbs'][] = ["control/player/edit/{$event_id}", 'edit'];
	
	$this->data['title'] = 'Music player - edit';
	$this->data['heading'] = $this->data['event']->title;
	return view("player/edit", $this->data);
}

private function find($event_id) {
	$event = $this->mdl_events->find($event_id);
	if(!$event) {
		$message = "Can't find event {$event_id}";
		throw \App\Exceptions\Exception::not_found($message);
	}
	return $event;
}

} 
