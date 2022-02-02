<?php namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;

class Teamtime extends \App\Controllers\BaseController {
	
use ResponseTrait;
	
public function index() {
	return $this->respondNoContent();
}

public function get($varname='', $key=null) {
	$tt_lib = new \App\Libraries\Teamtime;
	$response = $tt_lib::get_var($varname, $key);
	if($response && $key===null) $response = $response->value;
	if(!$response) return $this->fail("{$varname}/{$key} not found");
	return $this->respond($response);
}

public function admin() {
	if(!\App\Libraries\Auth::check_role('admin')) {
		return $this->failUnauthorized('Permission denied');
	}
	
	$tt_lib = new \App\Libraries\Teamtime;
	
	$get_var = $tt_lib->get_var("progtable");
	$progtable = $get_var ? $get_var->value : null;
	if(!$progtable) return $this->fail("No programme set");
	
	$get_var = $tt_lib->get_var("runvars");
	$runvars = $get_var ? $get_var->value : [];
	$getPost = $this->request->getPost('value');
	foreach($getPost as $key=>$val) {
		$runvars[$key] = $val;
	}
	
	$last_row = count($progtable) - 1 ;
	if($runvars['row']<1) $runvars['row'] = 1;
	if($runvars['row']>$last_row) $runvars['row'] = $last_row;
	$run_row = $progtable[$runvars['row']];
	$last_col = count($run_row) - 1;
	$mode = $run_row[0];
	
	// update run place 
	$start_place = "{$runvars['row']}-{$runvars['col']}";
	switch($runvars['cmd']) {
		case 'prev':
			if($mode=='o' || $mode=='t') {
				$runvars['row']--; 
				$runvars['col'] = 1;
			}
			else {
				$runvars['col']--;
				if($runvars['col']<1) {
					$runvars['col'] = $last_col;
					$runvars['row']--;
				}
			}
			break;
		case 'next':
			if($mode=='o' || $mode=='t') {
				$runvars['row']++; 
				$runvars['col'] = 1;
			}
			else {
				$runvars['col']++;
				if($runvars['col']>$last_col) {
					$runvars['col'] = 1;
					$runvars['row']++;
				}
			}
			break;
		case 'restart':
			$runvars['row'] = 1;
			$runvars['col'] = 1;
			break;
		case 'timer0':
			break;
	}
	if($runvars['row']<1) $runvars['row'] = 1;
	if($runvars['row']>$last_row) $runvars['row'] = $last_row;
	$runvars['mode'] = $progtable[$runvars['row']][0];
	$last_col = count($progtable[$runvars['row']]) - 1 ;
	if($runvars['col']<1) $runvars['col'] = 1;
	if($runvars['col']>$last_col) $runvars['col'] = $last_col;

	// timer move all this to library
	$runvars['timer'] = intval($runvars['timer']);
	if($runvars['mode']=='o') { // orientation
		$end_place = "{$runvars['row']}-{$runvars['col']}";
		if($end_place!=$start_place || $runvars['cmd']=='timer0') { 
			$runvars['timer_start'] = time(); // start timer
		}
		elseif(empty($runvars['timer_start'])) { // create start time if not there
			$runvars['timer_start'] = time();
		}
	}
	else { // cancel timer
		$runvars['timer_start'] = 0;
	}
		
	$id = "teamtime.runvars";
	$appvar = $tt_lib::$appvars->find($id);
	// don't write to DB unless necessary
	if($appvar) {
		$appvar->value = $runvars;
		if($appvar->hasChanged()) $tt_lib::$appvars->save($appvar);
	}
	else { // key doesn't exist; create it
		$appvar = new \App\Entities\Appvar;
		$appvar->id = $id;
		$appvar->value = $runvars;
		$tt_lib::$appvars->save_var($appvar);
	}
	
	$tt_lib = new \App\Libraries\Teamtime;
	$get_var = $tt_lib->get_var("runvars");
	return $this->respond($get_var->value);
}

public function display_view($ds_id=0, $dupd_request=0, $vupd_request=0) {
	$tt_lib = new \App\Libraries\Teamtime();
	
	$displays_var = $tt_lib::get_var('displays');
	$upd_check = $tt_lib::timestamp($displays_var->updated_at);
	if($upd_check>$dupd_request) {
		return $this->respond(['reload' => 'display'], 200);
	}
	
	$views = $tt_lib::get_var('views');
	$runvars = $tt_lib::get_var('runvars');
	$upd_check = [
		$tt_lib::timestamp($views->updated_at),
		$tt_lib::timestamp($runvars->updated_at)
	];
	$upd_check = max($upd_check);
	
	if($upd_check>$vupd_request) {
		// look up display and view
		$display = $tt_lib::get_var('displays', $ds_id);
		if(!$display) return $this->fail("Display {$ds_id} not found");
		$view = $tt_lib::display_view($display);
		if(!$view) return $this->fail("Can't find view for display {$ds_id}");
		// compile HTML for this view
		$view_html = $tt_lib::view_html($view['html']);
		if($view_html) $view['html'] = $view_html;
		$response = [
			'reload' => 'view',
			'updated' => $upd_check,
			'view' => $view
		];
		return $this->respond($response, 200);
	}
	return $this->respond(['reload' => 0], 200);
}

}
