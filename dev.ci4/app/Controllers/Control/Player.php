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
	
	// all tracks needed for this event
	$entries = [];
	foreach($event->entries() as $dis_key=>$dis) {
		foreach($dis->cats as $cat_key=>$cat) {
			foreach($cat->music as $exe) {
				$cat_entry = [
					'title' => "{$dis->name} {$cat->name}", 
					'exe' => $exe,
					'entries' => []
				];
				foreach($cat->entries as $entry) {
					$cat_entry['entries'][] = [
						'num' => $entry->num,
						'runorder' => implode('/', $entry->runorder)
					];
				}
				$entries[] = $cat_entry;
			}
		}	
	}
	$this->data['entries'] = $entries;
	
	if($cmd=='rebuild') {
		d($event->player);
		d($entries);

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