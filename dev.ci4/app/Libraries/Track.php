<?php namespace App\Libraries;

// read-only information on music field in entries table
// field format is array of exe=>check_state

// initialise
Track::$max_filesize = Track::ini_size(ini_get('upload_max_filesize'));

class Track { 

const type_allowed = 'audio';
const exts_allowed = ['wav','aac','aif','aiff','m4a','mp2','mp3','wma'];
static $max_filesize = 0; // max upload size [B]

// need these to work out file path
public $event_id = 0; 
public $entry_num = 0; 
public $exe = ''; // stored in db
public $check_state = 0; // stored in db 

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
	$attr = [
		'name' => 'trk',
		'value' => $this->filebase(),
		'data-url' => $url,
		'title' => sprintf('%s %s', $this->entry_num, strtoupper($this->exe))
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

public function view($opts=[]) {
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

	$ret = [];
	switch($icon) {
		case 'play-btn':
			$ret[] = sprintf('<span title="%s" class="btn btn-%s bi-%s" onclick="window.open(\'%s\', \'player\', \'width=500,height=100,menubar=0,status=0\')"></span>', 
				$title, $colour, $icon, $this->url());
			break;
		default: 
			$ret[] = sprintf('<span title="%s" class="text-%s bi-%s"></span>',
				$title, $colour, $icon);
	}
	if(in_array('checkbox', $opts)) {
		$ret[] = sprintf('<input name="trk_%s" type="checkbox" value="1" class="form-check-input">', $this->filebase());
	}
	return sprintf('<span class="track">%s</span>', implode(' ', $ret));
} 

public function status() {
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
	return $filename ? $this->urlpath() . $filename : '' ;
} 

public function filename($all=null) {
	$pattern = $this->filepath() . $this->filebase() . '.*';
	$files = glob($pattern); 
	if($all) return $files;
	// return first file with valid extension
	foreach($files as $file) {
		$arr = explode('.', $file); 
		$ext = end($arr);
		if(in_array($ext, self::exts_allowed)) return basename($file);
	}
	return '';
} 

public function filebase($extension='') {
	if($extension) $extension = ".$extension";
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

} 
