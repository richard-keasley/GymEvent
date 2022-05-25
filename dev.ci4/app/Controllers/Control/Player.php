<?php namespace App\Controllers\Control;

class Player extends \App\Controllers\BaseController {
	
function __construct() {
	$this->data['breadcrumbs'][] = "events";
	$this->mdl_events = new \App\Models\Events;
}

private function find($event_id) {
	$event = $this->mdl_events->find($event_id);
	if(!$event) throw new \RuntimeException("Can't find event $event_id", 404);
	return $event;
}

public function index() {
	$this->data['title'] = 'Music player';
	$this->data['heading'] = 'Music player';
	$this->data['events'] = $this->mdl_events->where('music', 2)->findAll();
	$this->data['breadcrumbs'][] = "player";
	$this->data['base_url'] = 'control/player/view';
	$this->data['body'] = <<<EOT
<p>The music service for these events is set to "view". There will be no more uploads for these events.</p>
EOT;
	return view('events/index', $this->data);
}

public function view($event_id=0) {
	$event = $this->find($event_id);
		
	$this->data['event'] = $event;
	$this->data['breadcrumbs'][] = $this->data['event']->breadcrumb();
	$this->data['breadcrumbs'][] = ["control/player/view/{$event_id}", 'player'];
	
	$this->data['title'] = 'Music player';
	$this->data['heading'] = $this->data['event']->title;
	return view("player/view", $this->data);
}

public function edit($event_id=0) {
	$event = $this->find($event_id);
	
	$cmd = $this->request->getPost('cmd');

	if($cmd=='update') { 
		//save player
		$player = $this->request->getPost('player');
		$player = json_decode($player);
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
	if($cmd=='upload') {
		$file = $this->request->getFile('file');
		$exe = $this->request->getPost('exe');
		$num = intval($this->request->getPost('num'));
		$error = '';
		
		if(!$error && !$file) $error = 'No file selected';
		if(!$error && !$exe) $error = 'No exercise entered';
		if(!$error && !$num) $error = 'No entry number entered';
		
		if(!$error && !$file->isValid()) $error = $file->getErrorString();
		if(!$error) {
			$extension = $file->getExtension();
			if(!in_array($extension, \App\Libraries\Track::exts_allowed)) $error = "{$extension} files are not allowed";
		}
		if(!$error) {
			$track = new \App\Libraries\Track();
			$track->event_id = $event->id; 
			$track->entry_num = $num; 
			$track->exe = $exe; 
			$track->check_state = 0; // unchecked

			// clear existing uploads
			$count = 0;
			foreach($track->filename(1) as $filename) {
				if(unlink($filename)) $count++;
			}
			if($count) $this->data['messages'][] = ["Existing track deleted", 'warning'];
			// store new upload
			$filepath = $track->filepath();
			$filename = $track->filebase($extension);
			if(!$file->move($filepath, $filename)) $error = $file->getErrorString();
		}
		// all done 
		if($error) $this->data['messages'][] = $error;
		else $this->data['messages'][] = ["Upload added", 'success'];
		#d($file);
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

public function auto($ch_id=0) {
	$ch_id = intval($ch_id);
	$channels = ['-', 'teamtime'];
	//view
	$this->data['ch_id'] = $ch_id;
	$this->data['channels'] = $channels;
	$this->data['breadcrumbs'][] = ["payer/auto", 'auto-player'];
	$this->data['title'] = 'Auto player';
	$this->data['heading'] = 'Auto player';
	return view("player/auto", $this->data);
}

} 