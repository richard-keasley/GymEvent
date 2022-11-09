<?php namespace App\Libraries;class Teamtime {const MIN_TICK = 500;const VIEWPATH = '/teamtime/displays/info/';static $updated = [];static $appvars = null;private static $vars = null;private static $_viewpath = null;static function init() {	self::$appvars = new \App\Models\Appvars();	$data = self::$appvars->like('id', 'teamtime.%')->findAll();	self::$vars = [];	foreach($data as $appvar) {		$varname = substr($appvar->id, 9);		self::$vars[$varname] = $appvar;	}		// validate displays	$appvar = empty(self::$vars['displays']) ? new \App\Entities\Appvar : self::$vars['displays'];	$displays = $appvar->value;	if(empty($displays)) $displays = [null];	foreach($displays as $ds_id=>$display) {		if(empty($display['title'])) $display['title'] = "Display $ds_id";		$display['tick'] = empty($display['tick']) ? self::MIN_TICK : intval($display['tick']);		if($display['tick']<self::MIN_TICK) $display['tick'] = self::MIN_TICK;		$display['view'] = empty($display['view']) ? 0 : intval($display['view']);		if(empty($data['style'])) $appval['style'] = "";		$displays[$ds_id] = $display;	}	self::$vars['displays']->value = $displays;			// validate views	$appvar = empty(self::$vars['views']) ? new \App\Entities\Appvar : self::$vars['views'];	$views = $appvar->value;	if(empty($views)) $views = [null, null];	foreach($views as $vw_id=>$view) {		if($vw_id) {			if(empty($view['title'])) $view['title'] = "View {$vw_id}";			$view['info'] = empty($view['info']) ? 0 : intval($view['info']);			$view['images'] = empty($view['images']) ? 0 : intval($view['images']);			if(!$view['images'] && !$view['info']) $view['info'] = 1;			if(empty($view['html'])) $view['html'] = "wait&hellip; ";		}		else $view = null ; // default view		$views[$vw_id] = $view;	}	self::$vars['views']->value = $views;			// validate runvars	$appvar = empty(self::$vars['runvars']) ? new \App\Entities\Appvar : self::$vars['runvars'];	$token = config('security')->tokenName;	$keys = ['cmd', 'view', 'row', 'col', 'message', 'mode', $token, 'timer', 'timer_start'];	$runvars = [];	foreach($keys as $key) {		$runvars[$key] = $appvar->value[$key] ?? '' ;	}	$runvars['timer_current'] = time() - $runvars['timer_start'];	self::$vars['runvars']->value = $runvars;		self::$_viewpath = realpath(config('Paths')->viewDirectory) . self::VIEWPATH;}static function get_vars() {	if(!self::$vars) self::init();	return self::$vars;}static function get_var($varname, $id=null) {	if(!self::$vars) self::init();	if(empty(self::$vars[$varname])) return null;	if($id===null) return self::$vars[$varname];	$value = self::$vars[$varname]->value;	return empty($value[$id]) ? null : $value[$id];}static function get_value($varname) {	$get_var = self::get_var($varname);	return $get_var ? $get_var->value : [] ;}static function get_viewpath() {	if(!self::$vars) self::init();	return self::$_viewpath;}static function display_view($display) {	// control panel view 	$vw_id = intval(self::get_var('runvars', 'view'));	// lookup default view for display	if(!$vw_id) $vw_id = $display['view'];	return self::get_var('views', $vw_id);}
static function view_html($view) {	$file = self::get_viewpath() . "{$view}.php";	$view = self::VIEWPATH . $view;	return file_exists($file) ? view($view) : $view;}static function get_images() {	$path = realpath(FCPATH . self::get_var('settings', 'image_path'));	$files = new \CodeIgniter\Files\FileCollection();	if($path) $files->addDirectory($path);	$pos = strlen(FCPATH);	$retval = [];	foreach($files as $file) {		$retval[] = base_url(substr($file, $pos));	}	return $retval;}static function timestamp($time) {	return $time->toLocalizedString('YYMMddHHmmss');}}