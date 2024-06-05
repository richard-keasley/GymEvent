<?php namespace App\Controllers\Setup;

class Scoreboard extends \App\Controllers\BaseController {
	
public function __construct() {
	$this->data['title'] = 'Setup scoreboard';
	$this->data['breadcrumbs'][] = 'admin';
	$this->data['breadcrumbs'][] = 'setup';
	$this->data['breadcrumbs'][] = ['setup/scoreboard', $this->data['title']];
}
	
public function index() {
	$appvars = new \App\Models\Appvars();
	$var_name = 'scoreboard.home';

	$html = $this->request->getPost('html');
	if($html) {
		$appvar = new \App\Entities\Appvar;
		$appvar->id = $var_name;
		$appvar->value = $html;
		$appvars->save_var($appvar);
	}	
	
	// view
	$this->data['heading'] = $this->data['title'];
	$this->data['html'] = $appvars->get_value($var_name);
	return view('scoreboard/setup', $this->data);
}

public function data($varname='') {
	$this->data['breadcrumbs'][] = ['setup/scoreboard/data', 'data'];
	$this->data['scoreboard'] = new \App\ThirdParty\scoreboard;

	$file = $varname ? $this->data['scoreboard']->get_file($varname) : '' ;

	if($file) {
		// read scoreboard database
		$sql = "SELECT * FROM `{$varname}`";
		$res = $this->data['scoreboard']->query($sql);
		if($res) {
			if($this->request->getPost('import')) {
				// overwrite existing data 
				$import = [
					'time' => date('Y-m-d H:i:s'),
					'table' => $res
				];
				$fileobj = $file->openFile('w');
				$fileobj->fwrite("<?php \n");
				$fileobj->fwrite("\$this->tables['{$varname}'] = " . var_export($import, 1) . ';');
				$this->data['scoreboard'] = new \App\ThirdParty\scoreboard;
				$this->data['messages'][] = ["{$varname} imported from scoreboard database", 'success'];
			}
						
			$this->data['tbody'] = $res;
			$this->data['varname'] = $varname;
			$this->data['breadcrumbs'][] = "setup/scoreboard/data/{$varname}";
			$this->data['heading'] = "Scoreboard - {$varname}";
			$this->data['title'] = $varname;
			return view('scoreboard/var', $this->data);
		}
	}
	if($this->data['scoreboard']->error) {
		$this->data['messages'][] = $this->data['scoreboard']->error;
	}		
	
	// view
	$this->data['heading'] = 'Scoreboard data';
	$this->data['title'] = 'Scoreboard data';
	return view('scoreboard/data', $this->data);
}

public function filters() {
	$appvars = new \App\Models\Appvars();
	$varnames = ['disciplines', 'exesets'];
	
	$filter = $this->request->getPost('filter');
	if($filter) {
		// save
		$appvar = new \App\Entities\Appvar;
		
		foreach($varnames as $varname) {
			$value = $this->request->getPost($varname);
			$value = explode(',', $value);
			$arr = [];
			foreach($value as $item) {
				$int = intval($item);
				if($int) $arr[] = $int;
			}
				
			$appvar->id = "scoreboard.{$varname}";
			$appvar->value = $arr;
			$appvars->save_var($appvar);		
		}
	}
	
	// read
	$this->data['filters'] = [];
	foreach($varnames as $varname) {
		$value = $appvars->get_value("scoreboard.{$varname}");
		if(!is_array($value)) $value = [];
		$this->data['filters'][$varname] = $value;
	}
		
	// view
	$this->data['breadcrumbs'][] = ['setup/scoreboard/data', 'data'];
	$this->data['breadcrumbs'][] = ['setup/scoreboard/data/filters', 'filters'];
	$this->data['heading'] = 'Scoreboard data filters';
	$this->data['title'] = 'Filters';
	return view('scoreboard/filters', $this->data);
	
	
}

}
