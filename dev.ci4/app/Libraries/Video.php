<?php namespace App\Libraries;

// read-only information on videos field in entries table
// field format is array of exe=>url

class Video {
	
public $event_id = 0; 
public $entry_num = 0; 
public $exe = ''; // stored in db
public $url = ''; // stored in db 

static function enabled() {
	return \App\Libraries\Auth::check_path('videos', 0) != 'disabled';
}

public function video() {
	return $this->error ? '' : $this->video;
}

public function view() {
	return $this->url ? 
		sprintf("<span onclick=\"window.open('%s', 'player', 'width=1024,height=766,menubar=0,status=0')\" class=\"btn btn-sm btn-success bi-play\" title=\"Display this video\"></span>", $this->url) : 
		'<span title="not found" class="text-danger bi-file-x"></span>' ;
}

// all these for local (uploaded) files. Some videos hosted elsewhere
public function filebase($extension='') {
	if($extension) $extension = ".$extension";
	return sprintf('%03d_%s%s', $this->entry_num, $this->exe, $extension);
}
public function filepath() {
	return FCPATH . "/public/events/{$this->event_id}/videos/";
}
public function urlpath() {
	return "/public/events/{$this->event_id}/videos/";
}



}