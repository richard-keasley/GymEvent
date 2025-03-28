<?php namespace App\Controllers;

class Music extends \App\Controllers\BaseController {

private $mdl_entries = null;
private $mdl_events = null;

public function __construct() {
	$this->mdl_entries = new \App\Models\Entries;
	$this->mdl_events = new \App\Models\Events;
	$this->data['title'] = "Music";
	$this->data['heading'] = "Music";
	$this->data['breadcrumbs'][] = 'events';
}
	
public function index() {
	// list all events with music=1
	$this->data['breadcrumbs'][] = 'music';
	$user_role = session('user_role');
	$user_id = intval(session('user_id'));
	
	// read
	$this->data['events'] = [];
	$events = $this->mdl_events->orderBy('date')->findAll();
	foreach($events as $event) {
		$perms = 0;
		if($event->music==1) {
			if($user_role==='superuser' || $user_role==='admin') {
				$perms = 1;
			}
			if($user_role==='club') {
				$entries = $event->user_entries($user_id);
				if(count($entries)) $perms = 1;
			}
		}
		
		if($perms) $this->data['events'][] = $event;
	}
	if(!count($this->data['events'])) {
		$this->data['messages'][] = ['There is no music available', 'danger'];
	}
	
	// view
	$this->data['body'] = 'music';
	$this->data['back_link'] = 'events';
	$this->data['base_url'] = 'music/view';
	return view('events/index', $this->data);
}

public function view($event_id=0) {
	$this->data['event'] = $this->mdl_events->find($event_id);
	if(!$this->data['event']) {
		$message = "Can't find event {$event_id}";
		throw \App\Exceptions\Exception::not_found($message);
	}
	
	$this->data['heading'] = $this->data['event']->title . ' - music';
	
	$filter = ['cat'=>'0'];
	$cat = $this->request->getGet('cat');
	if($cat) $filter['cat'] = $cat;
		
	// read 
	$entries = []; $cat_opts = ['0' => 'select category'];
	$discats = $this->mdl_entries->evt_discats($event_id, 0);
	foreach($discats as $dis) {
		$dis_cats = [];
		foreach($dis->cats as $cat) {
			if(count($cat->music)) {
				$cat_entries = $this->mdl_entries->cat_entries($cat->id);
				if($cat_entries) {
					$perm_entries = [];
					foreach($cat_entries as $entry) {
						// make this page user specific (even if you are admin)
						# if($entry->perm('music', 'view')) {
						if($entry->user_id == session('user_id')) {
							$perm_entries[] = $entry;
						}
					}
					if($perm_entries) {
						$cat_opts[$cat->id] = "{$dis->abbr} - {$cat->name}";
						if($filter['cat']=='0' || $filter['cat']==$cat->id) {
							$cat->entries = $perm_entries;
							$dis_cats[] = $cat; 
						}
					}
				}
			}
		}
		if(count($dis_cats)) {
			$dis->cats = $dis_cats;
			$entries[] = $dis;
		}
	}
	if(!count($cat_opts)) {
		$message = "There is no music to display";
		throw \App\Exceptions\Exception::forbidden($message);
	}
	
	# d($discats);
	// view
	$this->data['breadcrumbs'][] = $this->data['event']->breadcrumb();
	$this->data['breadcrumbs'][] = ["music/view/{$event_id}", 'music'];

	$this->data['filter'] = $filter;
	$this->data['cat_opts'] = $cat_opts;
	$this->data['entries'] = $entries;
	$this->data['users'] = $this->data['event']->users();

	switch($this->data['event']->music) {
		case 1: // edit
			$this->data['messages'][] = ['Clubs may upload new music for this event', 'info'];
			break;
		case 2: // view
		case 3: // finished
			$this->data['messages'][] = ['Music upload for this event is now complete. If you need to change your music, place the track on a USB stick and bring it to us <strong>as soon as you possibly can</strong>.', 'danger'];
			break;
		default: // waiting
			$this->data['messages'][] = ['The music service is closed for this event.', 'danger'];
	}
	return view('music/view', $this->data);
}

public function edit($entry_id=0) {
	$entry = $this->mdl_entries->find($entry_id);
	if(!$entry) {
		$message = "Can't find music {$entry_id}";
		throw \App\Exceptions\Exception::not_found($message);
	}
		
	if(!$entry->perm('music', 'edit')) {
		$message = 'Music is not available for editing';
		throw \App\Exceptions\Exception::forbidden($message);
	}
	
	$category = $entry->get_category();	
	if(!$category) {
		$message = "Category not found";
		throw \App\Exceptions\Exception::not_found($message);
	}

	if(!$category->music) {
		$message = "There is no music for this category";
		throw \App\Exceptions\Exception::exception($message, 400);
	}
	
	$event = $entry->get_event();

	// update 
	$cmd = $this->request->getPost('cmd');
	if($cmd=='upload') {
		$file = $this->request->getFile('file');
		$error = '';
		if(!$file) $error = 'No file selected';
		if(!$error && !$file->isValid()) $error = $file->getErrorString();
		if(!$error) {
			/*
			d($file);
			sometimes returns "bin" for "mp3" ... dunno why
			$extension = $file->getExtension();
			use client extension... that will be used after upload
			https://codeigniter.com/user_guide/libraries/uploaded_files.html
			*/
			$extension = strtolower($file->getClientExtension());
			if(!in_array($extension, \App\Libraries\Track::exts_allowed)) $error = "Files of type '{$extension}' are not allowed";
		}
		if(!$error) {
			$filesize = $file->getSizeByUnit('mb');
			$max_filesize = \App\Libraries\Track::$max_filesize;
			if($filesize > $max_filesize) $error = "Upload too large ({$filesize} MB). Please ensure uploaded tracks are less than {$max_filesize} MB.";
		}
		if(!$error) {
			$exe = $this->request->getPost('exe');
			$track = new \App\Libraries\Track();
			$track->event_id = $event->id; 
			$track->entry_num = $entry->num; 
			$track->exe = $exe; 
			$track->check_state = 0; // unchecked

			// clear existing uploads
			if($track->delete()) {
				$this->data['messages'][] = ["Existing track deleted", 'warning'];
			}
			
			// store new upload
			$filepath = $track->filepath();
			$filename = $track->filebase($extension);
			if(!$file->move($filepath, $filename)) $error = $file->getErrorString();
		}
		if(!$error) {
			$ent_music = $entry->music;
			$ent_music[$exe] = $track->check_state;
			$entry->music = $ent_music;
			if($entry->hasChanged('music')) {
				$entry->updateMusic();
			}
			// read
			$entry = $this->mdl_entries->find($entry_id);
		}
		// all done 
		if($error) $this->data['messages'][] = $error;
		else $this->data['messages'][] = ["Upload added", 'success'];
		#var_dump($file);
	}

	if($cmd=='copytrack') {
		$exe = $this->request->getPost('exe');
		$dest = new \App\Libraries\Track();
		$dest->event_id = $event->id; 
		$dest->entry_num = $entry->num; 
		$dest->exe = $exe; 
		$dest->check_state = 0; // unchecked
		
		try {
			$json = $this->request->getPost('src');
			if(!$json) throw new \exception("No source specified");
			$json = json_decode($json);
			if(!$json) throw new \exception("Invalid source parameters");
					
			$source = new \App\Libraries\Track();
			$source->event_id = $json->event ?? 0; 
			$source->entry_num = $json->entry?? 0; 
			$source->exe = $json->exe ?? '';
			$src_file = $source->file();
			if(!$src_file) throw new \exception("Could not find source track");
					
			// clear existing uploads
			if($dest->delete()) {
				$this->data['messages'][] = ["Existing track deleted", 'warning'];
			}
			
			// copy track
			$ext = $src_file->getExtension();
			$filebase = $dest->filebase($ext);
			$dest_file = $dest->filepath() . $dest->filebase($ext);
			if(!copy($src_file->getPathname(), $dest_file)) {
				throw new \exception("Could not copy track");
			}
			// update database
			$ent_music = $entry->music;
			$ent_music[$exe] = $dest->check_state;
			$entry->music = $ent_music;
			if($entry->hasChanged('music')) {
				$entry->updateMusic();
			}
			// read
			$entry = $this->mdl_entries->find($entry_id);
			// all done 
			$this->data['messages'][] = ["Please play the copied track to ensure it is correct!", 'success'];
		}
		catch(\throwable $ex) {
			$this->data['messages'][] = [$ex->getMessage(), 'danger'];
		}
	}

	// view
	$this->data['breadcrumbs'][] = $event->breadcrumb();
	$this->data['breadcrumbs'][] = ["music/view/{$event->id}", 'music'];
	$this->data['breadcrumbs'][] = $entry->breadcrumb('music');
	
	$this->data['heading'] = $event->title . ' - edit music';
	$this->data['event'] = $event;
	$this->data['entry'] = $entry;
	$model = new \App\Models\Users;
	$this->data['user'] = $model->find($entry->user_id);
	$this->data['category'] = $category;
	return view('music/edit', $this->data);
}

}
