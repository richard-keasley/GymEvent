<?php namespace App\Controllers;

class Music extends \App\Controllers\BaseController {

public function __construct() {
	$this->mdl_entries = new \App\Models\Entries();
	$this->mdl_events = new \App\Models\Events();
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
		if(session('user_id')) {
			$this->data['messages'][] = ['There is no music available', 'danger'];
		}
		else {
			throw new \RuntimeException('There is no music available', 401);
		}
	}
	// view
	$this->data['body'] = <<< EOT
<p>The music service is open for thee events. Please upload your music as soon as you can.</p>
EOT;
	$this->data['back_link'] = 'events';
	$this->data['base_url'] = 'music/view';
	$this->data['title'] = 'Video index';
	$this->data['heading'] = 'Select event to view';
	return view('events/index', $this->data);
}

public function view($event_id=0) {
	$this->data['event'] = $this->mdl_events->find($event_id);
	if(!$this->data['event']) throw new \RuntimeException("Can't find event $event_id", 404);
	
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
						if($entry->perm('music', 'view')) $perm_entries[] = $entry;
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
	$err_status = session('user_id') ? 403 : 401 ;
	if(!count($cat_opts)) {
		throw new \RuntimeException("There is no music to display", $err_status);
	}

	// view
	$this->data['breadcrumbs'][] = $this->data['event']->breadcrumb();
	$this->data['breadcrumbs'][] = ["music/view/{$event_id}", 'music'];

	$this->data['filter'] = $filter;
	$this->data['cat_opts'] = $cat_opts;
	$this->data['entries'] = $entries;
	switch($this->data['event']->music) {
		case 1: // edit
			$this->data['messages'][] = ['Clubs may upload new music for this event', 'info'];
			break;
		case 2: // view
		case 3: // finished
			$this->data['messages'][] = ['Music for this event is now complete. If you need to change your music, place the track on a USB stick and bring it to us <strong>as soon as you possibly can</strong>.', 'danger'];
			break;
		default: // waiting
			$this->data['messages'][] = ['The music service is closed for this event.', 'danger'];
	}
	return view('music/view', $this->data);
}

public function edit($entry_id=0) {
	$entry = $this->mdl_entries->find($entry_id);
	if(!$entry) throw new \RuntimeException("Can't find music $entry_id", 404);
	
	if(!$entry->perm('music', 'edit')) throw new \RuntimeException('Music is not available for editing', 403);
	$category = $entry->get_category();	
	if(!$category) throw new \RuntimeException("Category not found", 404); 
	if(!$category->music) throw new \RuntimeException("There is no music for this category", 400);
	
	$event = $entry->get_event();

	// update 
	if($this->request->getPost('upload')) {
		$file = $this->request->getFile('file');
		$error = '';
		if(!$file) $error = 'No file selected';
		if(!$error && !$file->isValid()) $error = $file->getErrorString();
		if(!$error) {
			#$mimetype = explode('/', $file->getMimeType());
			#d($file->getMimeType());
			#$type = empty($mimetype[0]) ? 'null' : $mimetype[0]  ;
			#if($type!=\App\Libraries\Track::type_allowed) $error = "{$type} files are not allowed";
			$extension = $file->guessExtension();
			if(!in_array($extension, \App\Libraries\Track::exts_allowed)) $error = "$extension files are not allowed";
		}
		if(!$error) {
			$filesize = $file->getSizeByUnit('mb');
			$max_filesize = \App\Libraries\Track::max_filesize;
			if($filesize > $max_filesize) $error = "Upload too large ($filesize MB). Please ensure uploaded tracks are less than $max_filesize MB.";
		}
		if(!$error) {
			$exe = $this->request->getPost('exe');
			$track = new \App\Libraries\Track();
			$track->event_id = $event->id; 
			$track->entry_num = $entry->num; 
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

	// view
	$this_url = $this->request->uri->__toString();
	$back_link = $this->request->getPost('back_link');
	if(!$back_link || $back_link==$this_url) {
		$back_link = session('_ci_previous_url');
	}
	if(!$back_link || $back_link==$this_url) {
		$back_link = base_url("music/view/{$event->id}");
	}
	$this->data['back_link'] = $back_link;	
	
	$this->data['breadcrumbs'][] = $event->breadcrumb();
	$this->data['breadcrumbs'][] = ["music/view/{$event->id}", 'music'];
	$this->data['breadcrumbs'][] = $entry->breadcrumb('music');
	
	$this->data['heading'] = $event->title . ' - edit music';
	$this->data['event'] = $event;
	$this->data['entry'] = $entry;
	$this->data['category'] = $category;
	return view('music/edit', $this->data);
}

}
