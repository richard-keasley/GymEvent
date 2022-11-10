<?php namespace App\Controllers\Control;
use \App\Libraries\Teamtime as tt_lib;

class Teamtime extends \App\Controllers\BaseController {
	
function __construct() {
	$this->data['breadcrumbs'][] = 'teamtime';
	$this->data['breadcrumbs'][] = ['control/teamtime', 'control'];
}

function index() {
	$this->data['breadcrumbs'] = null;
	$this->data['title'] = 'Control panel';
	$this->data['heading'] = 'Teamtime control panel';
	$this->data['head'] = '<link rel="stylesheet" type="text/css" href="/app/teamtime/admin.css">';
	return view('teamtime/admin/index', $this->data);
}

function settings() {
	if($this->request->getPost('save')) {
		// displays
		$inputs = [
			'event_id' => FILTER_SANITIZE_NUMBER_INT,
			'music_player' => FILTER_SANITIZE_STRING,
			'image_path' => FILTER_SANITIZE_URL,
			'run_rows' => FILTER_SANITIZE_STRING
		];
		$value = [];
		foreach($inputs as $key=>$filter) {
			$value[$key] = $this->request->getPost($key, $filter);
		}
		$value['run_rows'] = csv_array($value['run_rows']);
		$value['image_path'] = '/' . trim($value['image_path'], "/\\");
		// update
		tt_lib::save_value('settings', $value);
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
		tt_lib::save_value('progtable', $value);
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
	
	if($this->request->getPost('reload')) {
		// update
		$model = new \App\Models\Entries;
		$entries = $model->evt_discats($event_id);
		if($entries) {
			$sort = []; $value = [];
			foreach($entries as $dis) {
				foreach($dis->cats as $cat) {
					foreach($cat->entries as $rowkey=>$entry) {
						$value[] = [$entry->num, $entry->name];
						$sort[] = $entry->num;
					}
				}
			}
			array_multisort($sort, $value);
			tt_lib::save_value('teams', $value);
			$this->data['messages'][] = ['Teams updated', 'success'];
		}
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
		$value = json_decode($this->request->getPost('displays'), 1);
		tt_lib::save_value('displays', $value);
		// views
		$value = json_decode($this->request->getPost('views'), 1);
		array_unshift($value, null); // add default view 
		tt_lib::save_value('views', $value);
		
		$this->data['messages'][] = ['Displays and views updated', 'success'];
	}
	// view
	$this->data['breadcrumbs'][] = 'control/teamtime/displays';
	$this->data['title'] = 'Display set-up';
	$this->data['heading'] = 'Teamtime displays';		
	return view('teamtime/admin/displays', $this->data);
}

function player() {
	// view 
	$this->data['title'] = 'Music';
	$this->data['heading'] = 'Teamtime music';
	$this->data['breadcrumbs'][] = "control/teamtime/player";
	$this->data['back_link'] = "control/teamtime";
	return view("teamtime/admin/player", $this->data);
}

function appvars() {
	$this->data['title'] = 'Vars';
	$this->data['heading'] = 'Teamtime variables';
	$this->data['breadcrumbs'][] = "control/teamtime/appvars";
	return view("teamtime/admin/appvars", $this->data);
}

}
