<?php namespace App\Controllers\Admin;

class General extends \App\Controllers\BaseController {

public function __construct() {
	$this->data['title'] = "General Gymnastics";
	$this->data['heading'] = "General Gymnastics";
	$this->data['breadcrumbs'][] = 'admin';
	$this->data['breadcrumbs'][] = 'admin/general';
	$this->data['def_rules'] = new \App\Libraries\Rulesets\Fv_gold;	
}

public function index() {
	$cmd = $this->request->getPost('cmd');
	switch($cmd) {
		case 'upload':
		$file = $this->request->getFile('file');
		if($file->isValid()) {
			$filepath = \App\Libraries\General::filepath;
			if($file->move($filepath, $file->getClientName())) {
				$this->data['messages'][] = ["Upload added", 'success'];
			} else {
				$this->data['messages'][] = $file->getErrorString();
			}
		}
		else { 
			$this->data['messages'][] = $file->getErrorString();
		}
		break;
		
		case 'delfile':
		$key = $this->request->getPost('key');
		$files = \App\Libraries\General::files();
		$list = $files->get();
		$filename = $list[$key] ?? null;
		if($filename) {
			$basename = sprintf('<code>%s</code>', basename($filename));
			if(unlink($filename)) {
				$this->data['messages'][] = ["{$basename} deleted", 'success'];
			} 
			else { 
				$this->data['messages'][] = "Error deleting {$basename}";
			};
		}
		break;
	}
	
	$this->data['back_link'] = 'admin';
	return view('general/admin/index', $this->data);
}

public function rules($exe='') {
	// not needed 
	$this->data['title'] = strtoupper($exe);
	$this->data['heading'] = sprintf('General %s', $this->data['title']);
	$this->data['back_link'] = 'admin/general';
	$this->data['breadcrumbs'][] = ["admin/general/rules/{$exe}", $this->data['title']];
	$this->data['exe'] = $exe;
	return view("general/admin/rules/index", $this->data);
}

public function edit($exekey='') {
	$exekey = strtoupper($exekey);
	$exe_rules = $this->data['def_rules']->exes[$exekey] ?? null;
	if(!$exe_rules) {
		$message = "Can't find rule exercise {$exekey}";
		throw \App\Exceptions\Exception::not_found($message);
	}
 	
	// all data early so can be used for both functions
	$this->data['title'] = $exekey;
	$this->data['heading'] = sprintf('General %s', $exekey);
	$this->data['back_link'] = "admin/general";
	$this->data['breadcrumbs'][] = ["admin/general/edit/{$exekey}", $exe_rules['name']];
				
	$appvars = new \App\Models\Appvars();
	$appvar_id = strtolower("general.{$exekey}.skills");
	if($this->request->getPost('save')) {
		$fldnames = \App\Libraries\General::skills[$exekey] ?? null;
		if($fldnames) {
			// update
			$table = []; $row = [];
			$getPost = trim($this->request->getPost('value'));
			$lines = explode("\n", $getPost);
			foreach($lines as $line) {
				$line = explode("\t", trim($line));
				if(!$line) continue; // ignore blank rows
				$id = current($line);
				if($id=='id') continue; // it's the header row

				foreach($fldnames as $lnkey=>$fldname) {
					$row[$fldname] = trim($line[$lnkey] ?? '');
				}
				$table[$id] = $row;
			}
			$appvar = new \App\Entities\Appvar;
			$appvar->id = $appvar_id;
			$appvar->value = $table;
			$appvars->save_var($appvar);
			$this->data['messages'][] = ["'{$exekey}' updated", 'success'];
		}
		else {
			$this->data['messages'][] = ["Didn't recognize '{$exekey}'", 'danger'];
		}
	}
	 
	// read 
	$appvar = $appvars->find($appvar_id);
	$value = $appvar ? $appvar->value : null ;
	$textarea = [];
	if(is_array($value)) {
		foreach($value as $key=>$row) $textarea[] = implode("\t", $row);
	}
	$this->data['textarea'] = implode("\n", $textarea);
	$this->data['updated_at'] = $appvar ? $appvar->updated_at : null;
	$this->data['value'] = $value;
	// view
	return view("general/admin/edit", $this->data);
}
	
}
 