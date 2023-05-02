<?php namespace App\Libraries;

// read-only information on music field in entries table
// field format is array of exe=>check_state

// initialise
Track::$max_filesize = Track::ini_size(ini_get('upload_max_filesize'));

class Track { 

const type_allowed = 'audio';
const exts_allowed = ['mp3','ogg','wav','m4a','aac','aif','aiff','mp2','wma'];
static $max_filesize = 0; // max upload size [B]

// need these to work out file path
public $event_id = 0; 
public $entry_num = 0; 
public $exe = ''; // stored in db
public $check_state = 0; // stored in db 

static function enabled() {
	return \App\Libraries\Auth::check_path('music', 0) != 'disabled';
}

// class names for buttons
const BUTTONS = [
	'play' => 'btn bi m-1 btn-success bi-play-fill',
	'unchecked' => 'btn bi m-1 btn-warning bi-play-fill',
	'repeat' => 'btn bi m-1 btn-info bi-arrow-repeat',
	'pause' => 'btn bi m-1 btn-primary bi-pause-fill',
	'missing' => 'btn bi m-1 btn-danger bi-x'
];

static function ini_size($ini_size) {
	$suffix = strtoupper(substr($ini_size, -1));
	$suffixes = ['K', 'M', 'G', 'T', 'P'];
	$key = array_search($suffix, $suffixes);
	$ret = $key===false ? $ini_size : substr($ini_size, 0, -1) * pow(1024, $key + 1);
	return (int) $ret;
}

public function playbtn($opts=[]) {
	$ret = [];
	$track_url = $this->url();
	
	if(in_array('player', $opts)) {
		$label = $this->entry_num;
		$tag = 'button';
		$attrs = [
			'title' => $this->label(),
			'type' => 'button',
			'data-url' => $track_url,
			'name' => "trk",
			'style' => "min-width:4em;",
			'class' => $track_url ? self::BUTTONS['play'] : self::BUTTONS['missing'] 
		];
	}
	else {
		$label = '';
		$status = $this->status();
		switch($status) {
			case 'unchecked':
			$tag = 'button';
			$attrs = [
				'title' => 'This track has not been checked',
				'type' => 'button',
				'class' => self::BUTTONS['unchecked'],
				'onClick' => sprintf("playtrack.load('%s');", $track_url)
			];
			break;

			case 'ok': 
			$tag = 'button';
			$attrs = [
				'title' => "This track is ready for the event",
				'type' => 'button',
				'class' => self::BUTTONS['play'],
				'onClick' => sprintf("playtrack.load('%s');", $track_url)
			];
			break;

			case 'archived': 
			$tag = 'span';
			$attrs = [
				'title' => "This track has been safely stored",
				'class' => "text-success bi-archive"
			];
			break;
			
			case 'withdrawn': 
			$tag = 'span';
			$attrs = [
				'title' => "This track has been safely stored",
				'class' => "text-info bi-x-square"
			];
			break;
			
			default:
			$tag = 'span';
			$attrs = [
				'title' => "Track not found",
				'class' => "text-danger bi-x-square"
			];
		}
	}
	$ret[] = sprintf("<{$tag} %s>{$label}</{$tag}>", stringify_attributes($attrs));
	
	if(in_array('checkbox', $opts)) {
		$attrs = [
			'name' => "trk_" . $this->filebase(),
			'type' => "checkbox",
			'value' => "1",
			'class' => "form-check-input"
		];
		$ret[] = form_input($attrs);
	}
	
	return sprintf('<span class="track">%s</span>', implode(' ', $ret));
}

const state_labels = [
	'missing' => 'missing', 
	'unchecked' => 'unchecked', 
	'ok' => 'ok', 
	'archived' => 'archived', 
	'withdrawn' => 'withdrawn'
];
public function status() {
	// returns a state_label
	switch($this->check_state) {
		case 1: // ok
			return $this->file() ? 'ok' : 'archived' ;
		case 2: // withdrawn
			return 'withdrawn';
		default: // unchecked
			return $this->file() ? 'unchecked' : 'missing' ;
	}
} 

public function label() {
	$file = $this->file();
	$ext = $file ? $file->getExtension() : 'missing' ;
	return sprintf('%s %s (%s)', $this->entry_num, strtoupper($this->exe), $ext);
}

public function url() {
	$file = $this->file();
	if(!$file) return '';
	$query = ['t' => $file->getMTime()];
	return $this->urlpath() . $file->getFilename() . '?' . http_build_query($query);
} 

public function file() {
	// return preferred file for this track
	$ret_file = null; $ret_rank = 99;
	foreach($this->files() as $file) {
		$ext = strtolower($file->getExtension());
		$file_rank = array_search($ext, self::exts_allowed);
		if($file_rank===false) $file_rank = 99;
		if($file_rank<$ret_rank) {
			$ret_rank = $file_rank;
			$ret_file = $file;
		}		
	}
	return $ret_file;	
}

public function files($event=false) {
	// returns all files for this event or all files for this track
	$filepath = $this->filepath();
	if(!is_dir($filepath)) return [];
	
	$filebase = $event ? '' : $this->filebase() ;
	$regex = sprintf('#%s\.(%s)$#i', $filebase, implode('|', self::exts_allowed));
	$files = new \CodeIgniter\Files\FileCollection();
	$files->addDirectory($filepath);
	$files->retainPattern($regex);
	
	if(!$event) return $files;
	
	// remove duplicate tracks with preferred extension
	$ranks = []; 
	$ret_files = [];
	foreach($files as $file) {
		$ext = $file->getExtension();
		$key = $file->getBaseName(".{$ext}");
		$ext = strtolower($ext);
		$file_rank = array_search($ext, self::exts_allowed);
		if($file_rank===false) $file_rank = 99; // unsupported extension
		$done_rank = $ranks[$key] ?? 99 ; // 99 = not done yet

		if($file_rank<$done_rank) {
			// retain this file
			$ranks[$key] = $file_rank;
			$ret_files[$key] = $file;
		}
		# d($key, $ext, $file_rank, $done_rank);
	}
	# d($files->get(), $ranks, $ret_files);
	
	// rebuild file collection
	$files = new \CodeIgniter\Files\FileCollection();
	foreach($ret_files as $file) $files->addFile($file);
	return($files);
}

public function filebase($extension='') {
	if($extension) $extension = ".{$extension}";
	return sprintf('%03d_%s%s', $this->entry_num, strtoupper($this->exe), $extension);
} 

public function filepath() {
	return FCPATH . sprintf('public/events/%u/music/', $this->event_id);
} 

public function urlpath() {
	return sprintf('/public/events/%u/music/', $this->event_id);
} 

public function setFilename($filename) {
	// create track info when all you know is filename
	$arr = explode('.', $filename); 
	$filebase = $arr[0];
	$arr = explode('_', $filebase);
	$this->entry_num = empty($arr[0]) ? 0 : intval($arr[0]) ;
	$this->exe = empty($arr[1]) ? 'unknown' : $arr[1] ;
}

public function delete() {
	// clear existing uploads
	$count = 0;
	foreach($this->files() as $file) {
		if(unlink($file->getPathname())) $count++;
	}
	return $count;	
}

} 
