<?php namespace App\Controllers;

class Videos extends \App\Controllers\BaseController {

private $model = null;
	
function __construct() {
	$this->model = new \App\Models\Videos();
	$this->mdl_events = new \App\Models\Events();
	$this->mdl_entries = new \App\Models\Entries();
	$this->data['title'] = "Videos";
	$this->data['heading'] = "Videos";
	$this->data['breadcrumbs'][] = 'events';
}
	
public function index() {
	$user_role = session('user_role');
	$user_id = intval(session('user_id'));
	
	// read
	$this->data['events'] = [];
	$events = $this->mdl_events->orderBy('date')->findAll();
	foreach($events as $event) {
		$perms = 0;
		if($event->videos==1) {
			if($user_role==='superuser' || $user_role==='admin') {
				$perms = 1;
			}
			if($user_role==='club') {
				$entries = $event->user_entries($user_id);
				if(count($entries)) $perms = 1;
			}
		}
		if($event->videos==2) $perms = 1;
		
		if($perms) $this->data['events'][] = $event;
	}
	if(!count($this->data['events'])) $this->data['messages'][] = ['There are no videos available', 'danger'];
	
	// view
	$this->data['back_link'] = 'events';
	$this->data['base_url'] = 'videos/view';
	$this->data['title'] = 'Video index';
	$this->data['heading'] = 'Select event to view';
	$this->data['body'] = 'videos';
	return view('events/index', $this->data);
}

public function view($event_id=0) {
	$this->data['event'] = $this->mdl_events->find($event_id);
	if(!$this->data['event']) {
		$message = "Can't find event {$event_id}";
		throw \App\Exceptions\Exception::not_found($message);
	}
	
	$this->data['heading'] = $this->data['event']->title . ' - videos';
		
	$flds = ['catid']; $filter = [];
	foreach($flds as $fld) $filter[$fld] = $this->request->getGet($fld);
	$cat_id = intval(substr($filter['catid'], 1));

	// read 
	$entries = []; $cat_opts = [0 => 'select category'];
	$discats = $this->mdl_entries->evt_discats($event_id, 0);
	foreach($discats as $dis) {
		$dis_cats = [];
		foreach($dis->cats as $cat) {
			if(count($cat->videos)) {
				$perm_entries = []; // entries you're allowed to view
				$cat_entries = $this->mdl_entries->cat_entries($cat->id); // all entries
				foreach($cat_entries as $entry) {
					if($entry->perm('videos', 'view')) $perm_entries[] = $entry;
				}
				if($perm_entries) { // add it as option
					$cat_opts["c{$cat->id}"] = "{$dis->abbr} - {$cat->name}";
					if($cat->id==$cat_id) { // add it to view
						$cat->entries = $perm_entries;
						$dis_cats[] = $cat; 
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
		$message = "There are no videos to display";
		\App\Exceptions\Exception::forbidden($message);
	}

	// view
	$this->data['breadcrumbs'][] = $this->data['event']->breadcrumb();
	$this->data['breadcrumbs'][] = ["videos/view/{$event_id}", 'videos'];

	$this->data['filter'] = $filter;
	$this->data['cat_opts'] = $cat_opts;
	$this->data['entries'] = $entries;
	$this->data['users'] = $this->data['event']->users();

	switch($this->data['event']->videos) {
		case 1:
			$this->data['messages'][] = ['Clubs may upload new videos for this event', 'info'];
			break;
		case 2:
			$this->data['messages'][] = ['All videos for this event are available to view.', 'info'];
			break;
		default:// closed
			$this->data['messages'][] = ['The video service is closed for this event.', 'danger'];
	}
	return view('videos/view', $this->data);
}

public function edit($entry_id=0) {
	$entry = $this->model->find($entry_id);
	if(!$entry) {
		$message = "Can't find entry {$entry_id}";
		throw \App\Exceptions\Exception::not_found($message);
	}
	
	$category = $entry->get_category();	
	if(!$category) {
		$message = "Category not found";
		throw \App\Exceptions\Exception::not_found($message);
	}
	
	if(!$category->videos) if(!$category->music) {
		$message = "There are no videos for this category";
		\App\Exceptions\Exception::exception($message, 400);
	}
	
	$event = $entry->get_event();
	
	// update 
	if($this->request->getPost('save')) {
		$videos = $entry->videos;
		foreach($videos as $exe=>$url) {
			$videos[$exe] = $this->request->getPost($exe, FILTER_SANITIZE_URL);
		}
		$entry->videos = $videos;
	}
	if($this->request->getPost('upload')) {
		$file = $this->request->getFile('file');
		if($file) { 
			$ok = 0;
			if($file->isValid()) {
				$exe = $this->request->getPost('exe');
				$video = new \App\Libraries\Video;
				$video->event_id = $event->id;
				$video->entry_num = $entry->num;
				$video->exe = $exe;
								
				$filename = $video->filebase($file->getClientExtension());
				if($file->move($video->filepath(), $filename)) {
					$ok = 1;
					$url = $video->urlpath(). $filename;
					$videos = $entry->videos;
					$videos[$exe] = $url;
					$entry->videos = $videos;
				}
			}
			if($ok) {
				$this->data['messages'][] = ["Video uploaded for $exe", 'success'];
			}
			else {
				$error = $file->getErrorString();
				if(!$error) $error = "Error uploading file. Make sure it is not too big!";
				$this->data['messages'][] = $error;
			}
		}
		else $this->data['messages'][] = 'No file selected';
		#var_dump($file);
	}
	// check for updates
	if($entry->hasChanged('videos')) {
		$entry->updateVideos();
		$this->data['messages'][] = ["Videos updated", 'success'];
		$entry = $this->model->find($entry_id);
	}

	// view
	$this->data['breadcrumbs'][] = $event->breadcrumb();
	$this->data['breadcrumbs'][] = ["videos/view/{$event->id}", 'videos'];
	$this->data['breadcrumbs'][] = $entry->breadcrumb('videos');
	
	$model = new \App\Models\Users;
	$this->data['user'] = $model->find($entry->user_id);
	
	$this->data['heading'] = $event->title . ' - edit videos';
	$this->data['event'] = $event;
	$this->data['entry'] = $entry;
	$this->data['category'] = $category;
	return view('videos/edit', $this->data);
}

}