<?php namespace App\Controllers\Control;

class Teamtime extends \App\Controllers\BaseController {
	
function __construct() {
	$this->data['breadcrumbs'][] = 'teamtime';
	$this->data['breadcrumbs'][] = ['control/teamtime', 'control'];
	$this->data['tt_lib'] = new \App\Libraries\Teamtime();
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
		// update
		$appvars = new \App\Models\Appvars();
		$appvar = new \App\Entities\Appvar;
		// displays
		$value = $this->request->getPost();
		unset($value['save']);
		$value['run_rows'] = csv_array($value['run_rows']);
		$value['image_path'] = '/' . trim($value['image_path'], "/\\");
		$appvar->id = 'teamtime.settings';
		$appvar->value = $value;
		#d($appvar);
		$appvars->save_var($appvar);
		// reload
		$this->data['tt_lib'] = new \App\Libraries\Teamtime();
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
		$appvars = new \App\Models\Appvars();
		$appvar = new \App\Entities\Appvar;
		$appvar->id = 'teamtime.progtable';
		$appvar->value = $value;
		$appvars->save_var($appvar);
		// reload
		$this->data['tt_lib'] = new \App\Libraries\Teamtime();
		$this->data['messages'][] = ['Programme updated', 'success'];
	}
	// view
	$this->data['breadcrumbs'][] = 'control/teamtime/programme';
	$this->data['title'] = 'Programme';
	$this->data['heading'] = 'Teamtime Programme';		
	return view('teamtime/admin/programme', $this->data);
}

function teams() {
	if($this->request->getPost('save')) {
		// update
		$getPost = $this->request->getPost('teams');
		$value = [];
		foreach(explode("\n", $getPost) as $row) {
			$row = trim($row);
			if($row) {
				$arr = csv_array($row, 2);
				if($arr[1]) $value[] = $arr;
			}
		}
		$appvars = new \App\Models\Appvars();
		$appvar = new \App\Entities\Appvar;
		$appvar->id = 'teamtime.teams';
		$appvar->value = $value;
		$appvars->save_var($appvar);
		// reload
		$this->data['tt_lib'] = new \App\Libraries\Teamtime();
		$this->data['messages'][] = ['Teams updated', 'success'];
	}
	// view
	$this->data['breadcrumbs'][] = 'control/teamtime/teams';
	$this->data['title'] = 'teams';
	$this->data['heading'] = 'Teamtime teams';		
	return view('teamtime/admin/teams', $this->data);
}

function displays() {
	if($this->request->getPost('save')) {
		// update
		$appvars = new \App\Models\Appvars();
		$appvar = new \App\Entities\Appvar;
		// displays
		$value = json_decode($this->request->getPost('displays'), 1);
		$appvar->id = 'teamtime.displays';
		$appvar->value = $value;
		$appvars->save_var($appvar);
		// views
		$value = json_decode($this->request->getPost('views'), 1);
		array_unshift($value, null); // add default view 
		$appvar->id = 'teamtime.views';
		$appvar->value = $value;
		$appvars->save_var($appvar);
		// reload
		$this->data['tt_lib'] = new \App\Libraries\Teamtime();
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
