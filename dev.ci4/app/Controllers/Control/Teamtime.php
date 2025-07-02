<?php namespace App\Controllers\Control;
use \App\Libraries\Teamtime as tt_lib;

class Teamtime extends \App\Controllers\BaseController {
	
function __construct() {
	$this->data['breadcrumbs'][] = 'teamtime';
	$this->data['breadcrumbs'][] = ['control/teamtime', 'control'];
}

function index() {
	$this->data['breadcrumbs'] = null;
	$this->data['title'] = 'TT control';
	$this->data['heading'] = 'Teamtime control panel';
	$this->data['head'] = '<link rel="stylesheet" type="text/css" href="/app/teamtime/admin.css">';
	
	$cmd = $this->request->getPost('cmd');
	if($cmd=='progjump') {
		$row = intval($this->request->getPost('row'));
		$col = intval($this->request->getPost('col'));
		# d([$row, $col]);
	}
	
	$this->data['event_id'] = tt_lib::get_value("settings", "event_id");

	return view('teamtime/admin/index', $this->data);
}

function settings() {
	if($this->request->getPost('save')) {
		// displays
		$inputs = [
			'event_id' => FILTER_SANITIZE_NUMBER_INT,
			'music_player' => FILTER_SANITIZE_SPECIAL_CHARS,
			'run_rows' => FILTER_SANITIZE_SPECIAL_CHARS,
		];
		$value = [];
		foreach($inputs as $key=>$filter) {
			$value[$key] = $this->request->getPost($key, $filter);
		}
		$value['run_rows'] = csv_array($value['run_rows']);
				
		// update
		$error = tt_lib::save_value('settings', $value);
		if($error) $this->data['messages'][] = $error;		
		$this->data['messages'][] = ['Settings updated', 'success'];
	}
	// view 
	$this->data['title'] = 'settings';
	$this->data['heading'] = 'Teamtime settings';
	$this->data['breadcrumbs'][] = "control/teamtime/settings";
	return view("teamtime/admin/settings", $this->data);
}

function programme() {
	if($this->request->getPost('save')) {
		// update
		$getPost = trim($this->request->getPost('progtable'));
		$value = [];
		$mode = '';
		foreach(explode("\n", $getPost) as $row) {
			$row = trim($row);
			if($row) {
				$arr = csv_array($row);
				if(count($arr)) {
					if(count($arr)==1) $mode = 't';
					array_unshift($arr, $mode);
					$value[] = $arr;
					if($mode=='t') $mode = strtolower($arr[1][0]);
				}
			}
		}
		$error = tt_lib::save_value('progtable', $value);
		if($error) $this->data['messages'][] = $error;
		$this->data['messages'][] = ['Programme updated', 'success'];
	}
	// view
	$this->data['breadcrumbs'][] = 'control/teamtime/programme';
	$this->data['title'] = 'Programme';
	$this->data['heading'] = 'Teamtime Programme';		
	return view('teamtime/admin/programme', $this->data);
}

function teams() {
	$event_id = tt_lib::get_value('settings', 'event_id');
	
	$updated = [];
	if($this->request->getPost('reload')) {
		$model = new \App\Models\Entries;
		$entries = $model->evt_discats($event_id);
		if($entries) {
			foreach($entries as $dis) {
				foreach($dis->cats as $cat) {
					foreach($cat->entries as $rowkey=>$entry) {
						$updated[] = [$entry->num, $entry->name];
					}
				}
			}
		}
	}
	
	if($this->request->getPost('save')) {
		$getPost = trim($this->request->getPost('teams'));
		foreach(explode("\n", $getPost) as $row) {
			$row = trim($row);
			if($row) {
				$arr = csv_array($row, 2);
				if($arr) $updated[] = $arr;
			}
		}
	}
	
	if($updated) {
		// update
		array_multisort($updated);
		$error = tt_lib::save_value('teams', $updated);	
		if($error) $this->data['messages'][] = $error;
		$this->data['messages'][] = ['Teams updated', 'success'];
	}
	
	// view
	$this->data['breadcrumbs'][] = 'control/teamtime/teams';
	$this->data['title'] = 'teams';
	$this->data['heading'] = 'Teamtime teams';		
	$this->data['event_id'] = $event_id;		
	return view('teamtime/admin/teams', $this->data);
}

function displays() {
	if($this->request->getPost('save')) {
		// displays
		$value = filter_json($this->request->getPost('displays'));
		$error = tt_lib::save_value('displays', $value);
	
		// views
		$value = filter_json($this->request->getPost('views'));
		$error = tt_lib::save_value('views', $value);
		
		if($error) $this->data['messages'][] = $error;
		$this->data['messages'][] = ['Displays and views updated', 'success'];
	}
	// view
	$this->data['breadcrumbs'][] = 'control/teamtime/displays';
	$this->data['title'] = 'Display set-up';
	$this->data['heading'] = 'Teamtime displays';		
	return view('teamtime/admin/displays', $this->data);
}

function player($action='') {
	$event_id = tt_lib::get_value('settings', 'event_id');
	$mdl_events = new \App\Models\Events;
	$event = $mdl_events->find($event_id);
	
	$this->data['event_id'] = $event_id;
	$this->data['title'] = 'Teamtime player';
	$this->data['heading'] = $event->title;
	$this->data['h2'] = $event ? $event->title : 'Event not found' ;

	if($action=='save') {
		$this->data['showhelp'] = false;
		$this->data['breadcrumbs'] = null;
		$this->data['serviceworker'] = false;
	}
	else {
		$action = 'view';
		$this->data['breadcrumbs'][] = "control/teamtime/player";
	}
	$this->data['action'] = $action;

	// get view
	$page = view("player/teamtime", $this->data);
	
	return ($action=='save') ?
		$this->saveplayer($event_id, 'player.htm', $page) :
		$page ;
	
}

function appvars() {
	$this->data['title'] = 'Vars';
	$this->data['heading'] = 'Teamtime variables';
	$this->data['breadcrumbs'][] = "control/teamtime/appvars";
	return view("teamtime/admin/appvars", $this->data);
}

}
