<?php namespace App\Controllers\Admin;

class General extends \App\Controllers\BaseController {

public function __construct() {
	$this->data['title'] = "General Gymnastics";
	$this->data['heading'] = "General Gymnastics";
	$this->data['breadcrumbs'][] = 'admin';
	$this->data['breadcrumbs'][] = 'admin/general';
}

public function index() {
	$this->data['back_link'] = 'admin';
	return view('general/admin/index', $this->data);
}

public function rules($exe='') {
	$this->data['title'] = strtoupper($exe);
	$this->data['heading'] = sprintf('General %s', $this->data['title']);
	$this->data['back_link'] = 'admin/general';
	$this->data['breadcrumbs'][] = ["admin/general/rules/{$exe}", $this->data['title']];
	$this->data['exe'] = $exe;
	return view("general/admin/rules/index", $this->data);
}

public function edit($exe='', $varname='') {
	$appvars = new \App\Models\Appvars();
	$title = sprintf('%s %s', strtoupper($exe), $varname);
	switch($title) {
		case 'FX skills':
		case 'FX specials':
		case 'FX bonuses':
		case 'FX composition':
			$var_id = "general.{$exe}.{$varname}";
			break;
		default:
			$var_id = null;
	}
	if(!$var_id) throw new \RuntimeException("Can't find table {$title}", 404);
			
	if($this->request->getPost('save')) {
		// update
		switch($var_id) {
			case 'general.fx.skills' :
				$blank_line = \App\Libraries\General\Skills::blank;
				break;
			case 'general.fx.specials' :
				$blank_line = \App\Libraries\General\Specials::blank;
				break;
			case 'general.fx.bonuses' :
				$blank_line = \App\Libraries\General\Bonuses::blank;
				break;
			case 'general.fx.composition' :
				$blank_line = \App\Libraries\General\Composition::blank;
				break;
			default:
				$blank_line = null;
		}
		if($blank_line) {
			$table = []; $row = [];
			$getPost = trim($this->request->getPost('value'));
			$lines = explode("\n", $getPost);
			foreach($lines as $line) {
				$line = explode("\t", trim($line));
				if(!$line) continue; // ignore blank rows
				$id = current($line);
				if($id=='id') continue; // it's the header row
				$ln_key = 0;
				foreach($blank_line as $key=>$blank_cell) {
					$val = isset($line[$ln_key]) ? trim($line[$ln_key]) : $blank_cell;
					if(in_array($key, \App\Libraries\General\Skills::attributes)) {
						$val = $val ? 1 : 0 ;
					}
					else {
						switch(gettype($blank_cell)) {
							case 'integer': $val = intval($val); break;
							case 'double': $val = floatval($val); break;
							case 'boolean': $val = $val ? 1 : 0; break;
						}
					}
					$row[$key] = $val;
					$ln_key++;
				}
				$table[$id] = $row;
			}
			$appvar = new \App\Entities\Appvar;
			$appvar->id = $var_id;
			$appvar->value = $table;
			$appvars->save_var($appvar);
			$this->data['messages'][] = ["'{$title}' updated", 'success'];
		}
		else $this->data['messages'][] = ["Didn't recognize '{$title}'", 'danger'];
	}
	 
	// read 
	$appvar = $appvars->find($var_id);
	$value = $appvar ? $appvar->value : null ;
	$textarea = [];
	if(is_array($value)) {
		foreach($value as $key=>$row) $textarea[] = implode("\t", $row);
	}
	$this->data['textarea'] = implode("\n", $textarea);
	$this->data['updated_at'] = $appvar->updated_at;
	$this->data['value'] = $value;
	// view
	$this->data['title'] = $title;
	$this->data['heading'] = sprintf('General %s', $title);
	$this->data['back_link'] = "admin/general/rules/{$exe}";
	$this->data['breadcrumbs'][] = [$this->data['back_link'], strtoupper($exe)];
	$this->data['breadcrumbs'][] = ["admin/general/edit/{$exe}/{$varname}", $varname];
	return view("general/admin/rules/edit", $this->data);
}
	
}