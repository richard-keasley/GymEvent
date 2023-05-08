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
	$mailto = [];
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
									$mailto[] = $user->email;
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
		$count = 0;
		$email->setSubject($this->request->getPost('subject'));
		$email->setMessage($this->request->getPost('body'));
		$email->setBCC('richard@hawthgymnastics.co.uk');
		
		$error = null;
		foreach($mailto as $email_to) {
			if(ENVIRONMENT != 'production') $email_to = 'richard@base-camp.org.uk';
			$email->setTo($email_to);
			# d($email);
			if($email->send(false)) {
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
