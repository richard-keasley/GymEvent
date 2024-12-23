<?php namespace App\Libraries;

class Teamtime {
const MIN_TICK = 500;
const VIEWPATH = '/teamtime/displays/info/';

static $updated = [];
static $appvars = null;
private static $vars = null;
private static $_viewpath = null;

static function init() {
	self::$appvars = new \App\Models\Appvars();
	$data = self::$appvars->like('id', 'teamtime.%')->findAll();
	self::$vars = [];
	foreach($data as $appvar) {
		$varname = substr($appvar->id, 9);
		self::$vars[$varname] = $appvar;
	}
	
	// validate displays
	$appvar = empty(self::$vars['displays']) ? 
		new \App\Entities\Appvar : 
		self::$vars['displays'];
	$appval = $appvar->value;
	if(empty($appval)) $appval = [null];
	
	$template = [
		'title' => 'string',
		'tick' => 'integer',
		'view' => 'string',
		'style' => 'string'
	];
	$displays = [];
	foreach($appval as $ds_id=>$row) {
		if(!is_int($ds_id)) continue; // invalid display id
		$display = [
			'title' => $row['title'] ?? "Display {$ds_id}",
			'tick' => intval($row['tick'] ?? self::MIN_TICK),
			'view' => intval($row['view'] ?? 0),
			'style' => $row['style'] ?? ''
		];
		if($display['tick']<self::MIN_TICK) $display['tick'] = self::MIN_TICK;
		$displays[] = $display;
	}
	self::$vars['displays']->value = $displays;
		
	// validate views
	$appvar = empty(self::$vars['views']) ? new \App\Entities\Appvar : self::$vars['views'];
	$appval = $appvar->value;
	if(empty($appval)) $appval = [[]];
	// prepend default view
	$views = [[
		'title' => 'default',
		'info' => '',
		'images' => '',
		'html' => ''
	]]; 
	foreach($appval as $vw_id=>$row) {
		$view = [
			'title' => $row['title'] ?? "View {$vw_id}",
			'info' => intval($row['info'] ?? 0),
			'images' => intval($row['images'] ?? 0),
			'html' => $row['html'] ?? ''
		];
		if(!$view['images'] && !$view['info']) $view['info'] = 1;
		if(!$view['html']) $view['html'] = "wait&hellip; ";
		$views[] = $view;
	}
	self::$vars['views']->value = $views;	
	
	// validate runvars
	$appvar = empty(self::$vars['runvars']) ? 
		new \App\Entities\Appvar : 
		self::$vars['runvars'];
	$tokenName = config('Security')->tokenName;
	$runvars = [
		'cmd' => 'string', 
		'view' => 'integer', 
		'row' => 'integer', 
		'col' => 'integer', 
		'message' => 'string', 
		'mode' => 'string', 
		$tokenName => 'string', 
		'timer' => 'integer', 
		'timer_start' => 'integer'
	];
	foreach($runvars as $key=>$type) {
		$value = $appvar->value[$key] ?? '' ;
		$runvars[$key] = match($type) {
			'integer' => intval($value),
			default => $value
		};
	}
	$runvars['timer_current'] = time() - $runvars['timer_start'];
	self::$vars['runvars']->value = $runvars;
	
	self::$_viewpath = realpath(config('Paths')->viewDirectory) . self::VIEWPATH;
}

static function get_vars() {
	if(!self::$vars) self::init();
	return self::$vars;
}

static function get_var($varname, $id=null) {
	if(!self::$vars) self::init();
	# d($varname, self::$vars); return null;
	if(empty(self::$vars[$varname])) return null;
	if($id===null) return self::$vars[$varname];
	$value = self::$vars[$varname]->value;
	return empty($value[$id]) ? null : $value[$id];
}

static function get_value($varname, $id=null) {
	$get_var = self::get_var($varname);
	$value = $get_var->value ?? [] ;
	if($id===null) return $value;
	return $value[$id] ?? null ;
}

static function save_value($varname, $value) {
	if(!self::$vars) self::init();
	$id = "teamtime.{$varname}";
	$appvar = self::$appvars->find($id);
		
	if($appvar) {
		$appvar->value = $value;
		$updated = $appvar->hasChanged();
		if($updated) self::$appvars->save($appvar);
	}
	else { 
		// appvar doesn't exist; create it
		$appvar = new \App\Entities\Appvar;
		$appvar->id = $id;
		$appvar->value = $value;
		self::$appvars->save_var($appvar);
		$updated = true;
	}
	if(!$updated) return 0;
	
	self::init();

	// server sent events
	switch($varname) {
		case 'runvars':
		$arr = [
			'event' => match($value['cmd']) {
				'reload' => 'display',
				default => 'view'
			},
			# 'data' => '',
		];
		break;
		
		# $arr = ['event' => 'view'];		
		# break;

		case 'progtable':
		case 'views':
		case 'settings': 
		case 'displays':
		$arr = ['event' => 'display'];
		break;

		default:	
		$arr = null;
	}
	
	if(!$arr) return;
	// update stream
	$stream = new \App\Libraries\Sse\Stream('teamtime');
	$event = $stream->channel->read();
	$arr['id'] = $event->id ?? 0;
	if($arr['id'] > 999) $id = 1; else $arr['id']++;
	$arr['data'] = time();
	$event = new \App\Libraries\Sse\Event($arr);
	$success = $stream->channel->write($event);
}

static function get_viewpath() {
	if(!self::$vars) self::init();
	return self::$_viewpath;
}

static function display_view($display) {
	// control panel view 
	$vw_id = intval(self::get_value('runvars', 'view'));
	// lookup default view for display
	if(!$vw_id) $vw_id = $display['view'];
	return self::get_value('views', $vw_id);
}

static function view_html($viewname) {
	$file = self::get_viewpath() . "{$viewname}.php";
	return file_exists($file) ? 
		view(self::VIEWPATH . $viewname) : 
		$viewname;
}

static function get_images() {
	$event_id = self::get_value('settings', 'event_id');
	$files = new \CodeIgniter\Files\FileCollection();
	$image_path = realpath(FCPATH . "public/events/{$event_id}/teamtime");
	if($image_path) $files->addDirectory($image_path);
	
	$retval = [];
	$pos = strlen(FCPATH);
	foreach($files as $file) {
		$retval[] = base_url(substr($file, $pos));
	}
	return $retval;
}

static function timestamp($time) {
	return $time->toLocalizedString('YYMMddHHmmss');
}

static function get_rundata() {
	$rundata = [];
	$progtable = self::get_value('progtable');
	if(!$progtable) return $rundata;
	
	$rotations = [];
	$exes = reset($progtable);
	$mode = array_shift($exes); // discard mode
	$rnd = 0;
	$rot = 0;
	
	$rundata[0] = ['rnd'=>0, 'rot'=>0];
	foreach($exes as $exe) $rundata[0][$exe] = '';		
		
	foreach($progtable as $order=>$row) {
		$mode = array_shift($row);
		if($mode=='t') {
			$title = $row[0];
			$next_mode = strtolower($title[0]);
			if($next_mode=='c') {
				$tmp = explode('_', $title);
				$rnd = intval(array_pop($tmp));
				if(!isset($rotations[$rnd])) $rotations[$rnd] = 0 ;
				$rotations[$rnd]++;
				$rot = $rotations[$rnd];
			}
		}
		
		if($mode=='c') {
			foreach($row as $key=>$val) {
				$entry_num = intval($val);
				$exe = $exes[$key] ?? false;
				if($entry_num && $exe) {
					if(!isset($rundata[$entry_num])) {
						$rundata[$entry_num] = $rundata[0];
						$rundata[$entry_num]['rnd'] = $rnd;
						$rundata[$entry_num]['rot'] = $rot;
					}
					$rundata[$entry_num][$exe] = $order;
				}
			}
		}
	}	
	# d($rotations);
	return $rundata;
}

}
