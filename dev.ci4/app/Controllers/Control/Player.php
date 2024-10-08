<?php namespace App\Controllers\Control;

class Player extends \App\Controllers\BaseController {
	
function __construct() {
	$this->data['breadcrumbs'][] = "events";
	$this->mdl_events = new \App\Models\Events;
}

private function find($event_id) {
	$event = $this->mdl_events->find($event_id);
	if(!$event) {
		$message = "Can't find event {$event_id}";
		throw \App\Exceptions\Exception::not_found($message);
	}
	return $event;
}

private function post_track($event) {
	// gets track from post
	$exe = $this->request->getPost('exe');
	if(!$exe) {
		$this->data['messages'][] = 'Invalid track exercise';
		return null;
	}
	$entry_num = intval($this->request->getPost('entry_num'));	
	if(!$entry_num) {
		$this->data['messages'][] = 'Invalid track entry number';
		return null;
	}
	
	$track = new \App\Libraries\Track();
	$track->event_id = $event->id; 
	$track->entry_num = $entry_num; 
	$track->exe = $exe; 
	$track->check_state = 0; // unchecked
	return $track;
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
	$this->data['event'] = $this->find($event_id);
	$this->data['title'] = 'Music player';
	$this->data['heading'] = $this->data['event']->title;
	$this->data['action'] = $action;
	
	if($action=='save') {
		$this->data['breadcrumbs'] = null;
	}
	else {
		$this->data['breadcrumbs'][] = $this->data['event']->breadcrumb();
		$this->data['breadcrumbs'][] = ["control/player/view/{$event_id}", 'player'];
	}
	// get view
	$page = view("player/view", $this->data);	
	if($action!='save') return $page;

	// remove timestamp info
	$page = preg_replace('#\?t=\d+"#', '"', $page);
	// make paths relative and hide footers
	$replacements = [
		[base_url('app/'), 'app/'],
		[base_url("public/events/{$event_id}/music/"), 'music/'],
		['<footer ', '<footer style="display:none;" '],
	];
	foreach($replacements as $replacement) {
		$page = str_replace($replacement[0], $replacement[1], $page);
	}
		
	return $this->response->download('player.htm', $page);	
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
	
	if($cmd=='synch') {
		$track = $this->post_track($event);
		$success = $this->get_track($track);
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
	$this->data['breadcrumbs'][] = "control/player";
	$this->data['breadcrumbs'][] = ["control/player/auto", 'auto-player'];
	$this->data['title'] = 'Auto player';
	$this->data['heading'] = 'Auto player';
	return view("player/auto", $this->data);
}

private function get_track($track, $remote=null) {
	if(!$track) return false;
	if(!$remote) $remote = config('App')->hostname;
			
	$host = parse_url(base_url(), PHP_URL_HOST);
	if($host==$remote) {
		$this->data['messages'][] = "Already viewing source ({$remote})";
		return false;
	}
	
	$destpath = $track->filepath();
	if(!file_exists($destpath)) {
		$this->data['messages'][] = "{$destpath} does not exist";
		return false;
	}
	// get filename
	$url = "https://{$remote}/api/music/track_url/{$track->event_id}/{$track->entry_num}/{$track->exe}";
	$client = \Config\Services::curlrequest();
	$options = [
		'http_errors' => false
	];
	$response = $client->request('GET', $url, $options);
	$status = $response->getStatusCode();
	$source = $response->getBody();
	if($status > 300) {
		$this->data['messages'][] = "{$url} [{$status}]<br>synch failed";
		return false;
	}
	$uri = new \CodeIgniter\HTTP\URI($source);
	$filename = basename($uri->getPath());
	$destfile = $destpath . $filename;
	# $this->data['messages'][] = [$source, 'success'];
	# $this->data['messages'][] = [$destfile, 'success'];
	
	// get file
	$options = [
		'http_errors' => false,
		CURLOPT_HEADER => 0,
		CURLOPT_NOBODY => 0,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_FAILONERROR => false
	];
	$response = $client->request('GET', $source, $options);
	$status = $response->getStatusCode();
	$data = $response->getBody();
	if($status > 300) {
		$this->data['messages'][] = "{$server} [{$status}]: Couldn't get {$filename}";
		return false;
	}
	
	if($track->delete()) {
		$this->data['messages'][] = ["Existing track deleted. You may need to clear your browser cache to see the results.", 'warning'];
	}
	
	$bytes = file_put_contents($destfile, $data);
	if(!$bytes) {
		$this->data['messages'][] = "Failed to update {$filename} from {$server}";
		return false;
	}
	
	$this->data['messages'][] = ["Updated {$filename}.", 'success'];
	return true;
}

} 
