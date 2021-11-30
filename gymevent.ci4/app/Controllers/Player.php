<?php namespace App\Controllers;

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
	$this->data['base_url'] = 'player/view';
	$this->data['body'] = <<<EOT
<p>The music service for these events is set to "view". There will be no more uploads for these events.</p>
EOT;
	return view('events/index', $this->data);
}

public function view($event_id=0) {
	$event = $this->find($event_id);
		
	$this->data['event'] = $event;
	$this->data['breadcrumbs'][] = $this->data['event']->breadcrumb();
	$this->data['breadcrumbs'][] = ["player/view/{$event_id}", 'player'];
	
	$this->data['title'] = 'Music player';
	$this->data['heading'] = $this->data['event']->title;
	return view("player/view", $this->data);
}

public function admin($event_id=0) {
	$event = $this->find($event_id);
	if(\App\Libraries\Auth::check_role('admin')) {
		$player = $this->request->getPost('player');
		if(!is_null($player)) { //save player
			$event->player = json_decode($player);
			if($event->hasChanged()) {
				$this->data['messages'][] = ["Player info saved", 'success'];
				$this->mdl_events->save($event);
				$event = $this->mdl_events->find($event_id);
			}
		}
	}
	
	// all tracks needed for this event
	$event_tracks = []; 
	foreach($event->entries() as $dis_key=>$dis) {
		foreach($dis->cats as $cat_key=>$cat) {
			$tracks = [];
			foreach($cat->music as $exe) {
				foreach($cat->entries as $entry) {
					$tracks[$exe][] = $entry->num;
				}
			}
			if($tracks) {
				$key = sprintf('%03u-%03u', $dis_key, $cat_key); 
				$event_tracks[$key] = [
					'title' => "{$dis->name} {$cat->name}", 
					'tracks' => $tracks
				];
			}
		}	
	}
	$this->data['event_tracks'] = $event_tracks;
	
	$this->data['event'] = $event;
	$this->data['breadcrumbs'][] = $this->data['event']->breadcrumb();
	$this->data['breadcrumbs'][] = ["player/view/{$event_id}", 'player'];
	$this->data['breadcrumbs'][] = ["player/admin/{$event_id}", 'admin'];
	
	$this->data['title'] = 'Music player - admin';
	$this->data['heading'] = $this->data['event']->title;
	return view("player/admin", $this->data);
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