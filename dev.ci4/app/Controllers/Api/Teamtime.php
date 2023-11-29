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
	// get run place before any updates
	$row = $runvars['row'] ?? '#';
	$col = $runvars['col'] ?? '#';
	$start_place = "{$row}-{$col}";
		
	// update runvars according to post
	foreach($this->request->getPost() as $key=>$val) {
		$runvars[$key] = strip_tags($val);
	}
	
	// update run place 
	$mode = $progtable[$runvars['row']][0] ?? '';
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
		
		// stepping through competition?
		if(in_array($runvars['cmd'], ['next', 'prev']) && $mode=='c') {
			// skip empty cells
			$skip = $progtable[$runvars['row']][$runvars['col']]=='-';
		}
		else $skip = false;
		
	} while($skip);
	$runvars['mode'] = $mode;

	// have we moved?
	$end_place = "{$runvars['row']}-{$runvars['col']}";
	$moved = $end_place != $start_place;
	$runvars['moved'] = $moved;
	
	if($runvars['cmd']=='refresh' && $moved) {
		$runvars['cmd'] = 'moved';
	}
	
	if($runvars['cmd']=='reload') {
		$displays = tt_lib::get_value('displays');
		// add invalid display to force displays to be re-saved
		$displays['reload'] = [time()];
		$error = tt_lib::save_value('displays', $displays);
	}
				
	// update timer
	$runvars['timer'] = intval($runvars['timer']);
	$runvars['timer_start'] = intval($runvars['timer_start']);
	if($runvars['mode']=='o') { // orientation
		if($moved) $runvars['timer_start'] = time();
		if($runvars['cmd']=='timer0') $runvars['timer_start'] = time();
		if(!$runvars['timer_start']) $runvars['timer_start'] = time();
	}
	else { // cancel timer
		$runvars['timer_start'] = 0;
	}
	
	$error = tt_lib::save_value('runvars', $runvars);
	$runvars = tt_lib::get_value("runvars");
	$runvars['error'] = $error;
	return $this->respond($runvars);	
}

public function remote($varname='') {
	$error = false;
	
	if(!$error) {
		$remote = tt_lib::get_value('settings', 'remote');
		if($remote!='receive') $error = 'Remote not receiving';
	}
	if(!$error) {
		$remote_key = tt_lib::get_value('settings', 'remote_key');
		if(!$remote_key) $error = 'Remote key not set on receiver';
	}
	if(!$error) {
		$postval = $this->request->getPost('remote_key');
		if($remote_key!=$postval) $error = "Invalid remote key supplied";
	}
	if(!$error) {
		$postval = $this->request->getPost('value');
		if(!$postval) $error = "No data sent for {$varname}";
	}
	if($error) return $this->failUnauthorized($error);
	
	// update 
	# $error = "remote control disabled";
	if(!$error) {
		$error = tt_lib::save_value($varname, $postval);
	}
	
	return $error ? $this->fail($error) : $this->respond('OK');
}

public function display_view($ds_id=0, $ds_updated=0, $vw_updated=0) {
	$reload = false;
	
	// when were displays updated?
	$displays_var = tt_lib::get_var('displays');
	$updated = tt_lib::timestamp($displays_var->updated_at);
	if($updated>$ds_updated) $reload = true;
	if($reload) return $this->respond(['reload' => 'display'], 200);
		
	// when were views or runvars updated?
	$views_var = tt_lib::get_var('views');
	$runvars_var = tt_lib::get_var('runvars');
	$updated = [
		tt_lib::timestamp($views_var->updated_at),
		tt_lib::timestamp($runvars_var->updated_at)
	];
	$updated = max($updated);
	if($updated>$vw_updated) $reload = true;
		
	// nothing to update
	if(!$reload) return $this->respond(['reload' => false], 200);
		
	// look up display and view
	$display = tt_lib::get_value('displays', $ds_id);
	if(!$display) return $this->fail("Display {$ds_id} not found");
	$view = tt_lib::display_view($display);
	if(!$view) return $this->fail("Can't find view for display {$ds_id}");
	// compile HTML for this view
	$view['html'] = tt_lib::view_html($view['html']);
	$response = [
		'reload' => 'view',
		'updated' => $updated,
		'view' => $view
	];
	return $this->respond($response, 200);
}


}
