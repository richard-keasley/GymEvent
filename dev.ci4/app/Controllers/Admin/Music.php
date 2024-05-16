<?php namespace App\Controllers\Admin;

class Music extends \App\Controllers\BaseController {
	
private $mdl_events = null;
private $mdl_entries = null;

private function find($event_id) {
	$this->mdl_events = new \App\Models\Events;
	$this->mdl_entries = new \App\Models\Entries;
	$this->data['event'] = $this->mdl_events->find($event_id);
	if(!$this->data['event']) {
		$message = "Can't find event {$event_id}";
		throw \App\Exceptions\Exception::not_found($message);
	}
	
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
	
	// view
	$this->data['messages'][] = match($this->data['event']->music) {
		0 => ['Music upload for this event is not yet open', 'warning'],
		1 => ['Music upload for this event is open', 'success'],
		2 => ['Music upload for this event is completed', 'warning'],
		3 => ['Music upload service is closed for this event', 'success'],
		default => ["Unknown state ({$this->data['event']->clubrets}) for club returns", 'danger']
	};
		
	$this->data['users'] = $this->data['event']->users();
	$this->data['entries'] = $this->data['event']->entries();
	$this->data['title'] = $this->data['event']->title;
	$this->data['heading'] = $this->data['event']->title;
	return view('music/admin', $this->data);
}

public function clubs($event_id=0) {
	$this->find($event_id);
	$this->data['users'] = $this->data['event']->users();
	$this->data['entries'] = $this->data['event']->entries();
	
	$status = $this->request->getGet('status');
	if(!isset(\App\Libraries\Track::state_labels[$status])) $status = 0;
	$this->data['state_labels'] = $status ? [$status] : \App\Libraries\Track::state_labels;
	$this->data['status'] = $status;
		
	// build table
	$recipients = [];
	$tbody = []; $orderby = [];
	$track = new \App\Libraries\Track();
	$track->event_id = $event_id;
	$new_row = ['club' => ''];
	foreach($this->data['state_labels'] as $state_label) $new_row[$state_label] = 0;
	foreach($this->data['entries'] as $dis) {
		foreach($dis->cats as $cat) {
			foreach($cat->entries as $entry) {
				$user_id = $entry->user_id;
				$track->entry_num = $entry->num;
				foreach($entry->music as $exe=>$check_state) {
					$track->exe = $exe;
					$track->check_state = $check_state;
					$state_label = $track->status();
					
					if(in_array($state_label, $this->data['state_labels'])) {
						if(!isset($tbody[$user_id])) {
							$tbody[$user_id] = $new_row;
							$user = $this->data['users'][$user_id] ?? null ;
							if($user) {
								$club = anchor("/admin/music/view/{$event_id}?user={$user_id}", $user->name) . ' ' . $user->link() ;
								if($user->email) {
									$club .= ' ' . mailto($user->email, '<i class="bi-envelope"></i>', ['title' => $user->email]);
									$recipients[] = $user;
								}
								$orderby[$user_id] = $user->name;
								$tbody[$user_id]['club'] = $club;
							}
							else {
								$tbody[$user_id]['club'] = '[unkown]';
								$orderby[$user_id] = '';
							}
						}
						$tbody[$user_id][$state_label]++;
					}
				}
			}
		}
	}
	array_multisort($orderby, $tbody);
	$this->data['tbody'] = $tbody;
	
	if($this->request->getPost('sendmail')) {
		$email = \Config\Services::email();
		$email_subject = $this->request->getPost('subject');
		$email_template = $this->request->getPost('body');
		$email_template = str_replace('ph:{', '{', $email_template);
		$app_mailto = config('App')->mailto;

		$parser = \Config\Services::parser();
		$placeholders = [
			'event' => $this->data['event']->placeholders()
		];		
		
		$count = 0;
		$error = null;
		foreach($recipients as $recipient) {
			$placeholders['user'] = $recipient->placeholders();
			$translate = array_flatten_with_dots($placeholders);
			$email_message = $parser->setData($translate)->renderString($email_template);
			$email->setMessage($email_message);

			$email->setSubject($email_subject);
			$email->setBCC($app_mailto);
			$email_to = (ENVIRONMENT == 'production') ? $recipient->email : $app_mailto;
			$email->setTo($email_to);
			# d($translate); d($email_message); 
			# d($email); 
			# die;
			
			if($email->send()) {
				$count++; 
			}
			else { 			
				$error = $email->printDebugger(['header']);
				if(!$error) $error = "Email error ({$email_to})";
			}
		}
		if($error) $this->data['messages'][] = [$error, 'danger'];
		if($count) $this->data['messages'][] = ["{$count} emails sent", 'success'];
	}
	
	// view
	$this->data['breadcrumbs'][] = ["admin/music/clubs/{$event_id}", 'clubs'];
			
	$this->data['title'] = $this->data['event']->title;
	$this->data['heading'] = $this->data['event']->title;
	
	return view('music/clubs', $this->data);
}

}
