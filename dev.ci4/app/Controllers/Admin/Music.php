<?php namespace App\Controllers\Admin;

class Music extends \App\Controllers\BaseController {

function __construct() {
	$this->data['breadcrumbs'][] = 'admin';
	$this->data['breadcrumbs'][] = 'admin/events';
}
	
public function index() {
	$events_model = new \App\Models\Events();
	$this->data['events'] = $events_model->whereIn('music', [1,2])->orderBy('date')->findAll();

	$this->data['breadcrumbs'][] = 'admin/music';
	$this->data['base_url'] = 'admin/music/view';
	$this->data['body'] = <<< EOT
<p>The music service for these events is either "open for uploads" (state 1) or "completed" (state 2). You should close the music service (state 3) for an event once all music has been verified.</p>
<p>From here:</p>
<ul>
<li>set check state of entries' tracks</li>
<li>play / upload / delete / download tracks for event</li>
<li>list event's music by discipline / club / status</li>
<li>see disk space used</li>
</ul>
EOT;
	return view('events/index', $this->data);
}

public function view($event_id=0) {
	$events_model = new \App\Models\Events();
	$this->data['event'] = $events_model->find($event_id);
	if(!$this->data['event']) throw new \RuntimeException("Can't find event $event_id", 404);
	
	
	d($this->request->getPost());

	if($this->request->getPost('set_state')) {
		$data = [
			'id' => $event_id,
			'music' => intval($this->request->getPost('music'))
		];
		d($data);
		$events_model->save($data);
		$this->data['event'] = $events_model->find($event_id);
	}
	
	$method = $this->request->getPost('method');
	if(in_array($method, ['set_check', 'delete'])) {
		$getPost = $this->request->getPost();
		$value = $this->request->getPost('val');
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
							if($getPost['method']=='set_check') {
								$ent_music[$exe] = $getPost['val'];
							}
							if($getPost['method']=='delete') {
								// clear existing uploads
								$count = 0;
								foreach($track->filename(1) as $filename) {
									if(unlink($filename)) $count++;
								}
								if($count) $this->data['messages'][] = ["Existing track deleted", 'warning'];
							}
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
	
	$entries_model = new \App\Models\Entries;
	$this->data['users'] = $entries_model->evt_users($event_id);
			
	$this->data['filter'] = []; 
	$flds = ['dis', 'cat', 'user', 'status'];
	foreach($flds as $fld) $this->data['filter'][$fld] = $this->request->getGet($fld);
		
	$this->data['breadcrumbs'][] = $this->data['event']->breadcrumb(null, 'admin');
	$this->data['breadcrumbs'][] = ["admin/music/view/{$event_id}", 'music'];
	$this->data['entries'] = $this->data['event']->entries();
	$this->data['title'] = $this->data['event']->title;
	$this->data['heading'] = $this->data['event']->title;
	return view('music/admin', $this->data);
}

}
