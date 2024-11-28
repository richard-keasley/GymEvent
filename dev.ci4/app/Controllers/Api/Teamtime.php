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
	if(strtolower($this->request->getMethod())!='post') {
		return $this->get('runvars');
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

public function getview($ds_id='#') {
	// look up display and view
	$display = tt_lib::get_value('displays', $ds_id);
	if(!$display) return $this->fail("Display {$ds_id} not found");
	$view = tt_lib::display_view($display);
	if(!$view) return $this->fail("Can't find view for display {$ds_id}");
	
	// compile HTML for this view
	$view['html'] = tt_lib::view_html($view['html']);
	
	$stream = new \App\Libraries\Sse\Stream('teamtime');
	$event = $stream->channel->read();
	
	$response = [
		'view' => $view,
		'updated' => $event->updated,
		'event' => $event->id,
	];
	return $this->respond($response, 200);
}


}
