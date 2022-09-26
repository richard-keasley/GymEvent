<?php namespace App\Libraries;

// read-only information on music field in entries table
// field format is array of exe=>check_state

// initialise
Track::$max_filesize = Track::ini_size(ini_get('upload_max_filesize'));

class Track { 

const type_allowed = 'audio';
const exts_allowed = ['aac','aif','aiff','m4a','mp2','mp3','ogg','wav','wma'];
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
const BUTTON_PLAY = 'btn bi m-1 btn-success bi-play-fill';
const BUTTON_REPEAT = 'btn bi m-1 btn-info bi-arrow-repeat';
const BUTTON_PAUSE = 'btn bi m-1 btn-primary bi-pause-fill';
const BUTTON_MISSING = 'btn bi m-1 btn-danger bi-x';

static function js_buttons() {
	return implode("\n", [
		"const BUTTON_PLAY = '" . self::BUTTON_PLAY . "';",
		"const BUTTON_REPEAT = '" . self::BUTTON_REPEAT . "';",
		"const BUTTON_PAUSE = '" . self::BUTTON_PAUSE . "';",
		"const BUTTON_MISSING = '" . self::BUTTON_MISSING . "';"
	]);
}

static function ini_size($ini_size) {
	$suffix = strtoupper(substr($ini_size, -1));
	$suffixes = ['K', 'M', 'G', 'T', 'P'];
	$key = array_search($suffix, $suffixes);
	$ret = $key===false ? $ini_size : substr($ini_size, 0, -1) * pow(1024, $key + 1);
	return (int) $ret;
}

public function button() {
	$label = $this->entry_num;
	$url = $this->url();
	$onclick = 'playtrack.play()';
	$attr = [
		'name' => 'trk',
		'value' => $this->filebase(),
		'data-url' => $url,
		'title' => sprintf('%s %s', $this->entry_num, strtoupper($this->exe)),
		'type' => 'button',
		'onclick' => ''
	];
	if($url) {
		$attr['class'] = self::BUTTON_PLAY;
	}
	else {
		$attr['class'] = self::BUTTON_MISSING;
		$attr['title'] .= ' (missing)';
	}	
	return sprintf('<button %s>%s</button>', stringify_attributes($attr), $label);
} 

public function playbtn($opts=[]) {
	$ret = [];

	if(in_array('player', $opts)) {
		$url = $this->url();
		$attr = [
			'title' => sprintf('%s %s', $this->entry_num, strtoupper($this->exe)),
			'type' => 'button',
			'data-url' => $url,
			'name' => "track"
		];
		if($url) {
			$attr['class'] = self::BUTTON_PLAY;
		}
		else {
			$attr['class'] = self::BUTTON_MISSING;
			$attr['title'] .= ' (missing)';
		}	
		$ret[] = sprintf('<button %s>%s</button>', stringify_attributes($attr), $this->entry_num);
	}
	else {
		$status = $this->status();
		switch($status) {
			case 'unchecked': 
				$icon = 'play-btn'; 
				$colour = 'warning'; 
				$title = 'This track has not been checked';
				break;
			case 'ok': 
				$icon = 'play-btn'; 
				$colour = 'success'; 
				$title = 'This track is ready for the event';
				break;
			case 'archived': 
				$icon = 'archive'; 
				$colour = 'success'; 
				$title = 'This track has been safely stored';
				break;
			case 'withdrawn': 
				$icon = 'x-square'; 
				$colour = 'info'; 
				$title = 'This exercise has been withdrawn';
				break;
			default:
				$icon = 'file-x'; 
				$colour = 'danger'; 
				$title = 'Track not found';
		}

		switch($icon) {
			case 'play-btn':
				$attrs = [
					'title' => $title,
					'class' => "btn btn-{$colour} bi-{$icon}",
					'onClick' => sprintf("playtrack.play('%s');", $this->url())
				];
				break;
			default:
				$attrs = [
					'title' => $title,
					'class' => "text-{$colour} bi-{$icon}"
				];	
		}
		$ret[] = sprintf('<span %s></span>', stringify_attributes($attrs));
	}
	
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
			return $this->filename() ? 'ok' : 'archived' ;
		case 2: // withdrawn
			return 'withdrawn';
		default: // unchecked
			return $this->filename() ? 'unchecked' : 'missing' ;
	}
} 

public function url() {
	$filename = $this->filename();
	if(!$filename) return '';
	// allows cache to work for 20 seconds
	return $this->urlpath() . $filename . '?t=' . floor(time() / 20) ;
} 

public function filename() {
	// return first filename for this track
	$files = $this->files();
	return count($files) ? $files->getIterator()->current()->getFilename() : '';
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
	return $files;
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
