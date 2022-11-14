<?php namespace App\Controllers\Api;
use \App\Libraries\Teamtime as tt_lib;
use CodeIgniter\API\ResponseTrait;

class Teamtime extends \App\Controllers\BaseController {
	
use ResponseTrait;
	
public function index() {
	return $this->respondNoContent();
}

public function get($varname='', $key=null) {
	$response = tt_lib::get_value($varname, $key);
	return is_null($response) ?
		$this->fail("{$varname}/{$key} not found") :
		$this->respond($response) ;
}

public function control() {
	if(!\App\Libraries\Auth::check_role('controller')) {
		return $this->failUnauthorized('Permission denied');
	}
	
	$progtable = tt_lib::get_value("progtable");
	if(!$progtable) return $this->fail("No programme set");
	
	$runvars = tt_lib::get_value("runvars");
	// modify runvars according to post
	$getPost = $this->request->getPost(null, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
	foreach($getPost as $key=>$val) {
		$runvars[$key] = $val;
	}
	
	// update run place 
	$mode = $progtable[$runvars['row']][0];
	$start_place = "{$runvars['row']}-{$runvars['col']}";
	do {
		switch($runvars['cmd']) {
			case 'prev':
				if($mode=='o' || $mode=='t') {
					$runvars['row']--; 
					$runvars['col'] = 99;
				}
				else {
					$runvars['col']--;
					if($runvars['col']<1) {
						$runvars['row']--;
						$runvars['col'] = 99;
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
					$last_col = count($progtable[$runvars['row']]) - 1 ;
					if($runvars['col']>$last_col) {
						$runvars['row']++;
						$runvars['col'] = 1;
					}
				}
				break;
			case 'restart':
				$runvars['row'] = 1;
				$runvars['col'] = 1;
				break;
		}
		// ensure we're still in the table
		$last_row = count($progtable) - 1 ;
		if($runvars['row']<1) $runvars['row'] = 1;
		if($runvars['row']>$last_row) $runvars['row'] = $last_row;
		$last_col = count($progtable[$runvars['row']]) - 1 ;
		if($runvars['col']<1) $runvars['col'] = 1;
		if($runvars['col']>$last_col) $runvars['col'] = $last_col;
		$mode = $progtable[$runvars['row']][0];
		// if competing, are we in empty cell?
		$empty_cell = $mode=='c' && $progtable[$runvars['row']][$runvars['col']]=='-';
	} while($empty_cell);
	$runvars['mode'] = $mode;
		
	// update timer
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
	
	tt_lib::save_value('runvars', $runvars);
	return $this->respond($runvars);
}

public function display_view($ds_id=0, $dupd_request=0, $vupd_request=0) {
	$displays_var = tt_lib::get_var('displays');
	$upd_check = tt_lib::timestamp($displays_var->updated_at);
	if($upd_check>$dupd_request) {
		return $this->respond(['reload' => 'display'], 200);
	}
	
	$views_var = tt_lib::get_var('views');
	$runvars_var = tt_lib::get_var('runvars');
	$upd_check = [
		tt_lib::timestamp($views_var->updated_at),
		tt_lib::timestamp($runvars_var->updated_at)
	];
	$upd_check = max($upd_check);
	
	if($upd_check>$vupd_request) {
		// look up display and view
		$display = tt_lib::get_value('displays', $ds_id);
		if(!$display) return $this->fail("Display {$ds_id} not found");
		$view = tt_lib::display_view($display);
		if(!$view) return $this->fail("Can't find view for display {$ds_id}");
		// compile HTML for this view
		$view['html'] = tt_lib::view_html($view['html']);
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
