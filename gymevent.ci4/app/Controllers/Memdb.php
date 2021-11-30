<?php namespace App\Controllers;

class Memdb extends \App\Controllers\BaseController {
	
function __construct() {
	$this->data['title'] = 'MemberDB';
	$this->data['heading'] = 'Membership Database';
}
	
public function index() {
	return view('memdb/index', $this->data);
}

public function help($page='index') {
	$view = "memdb/help/{$page}";
	$include = VIEWPATH . "{$view}.php";
	$heading = anchor(base_url('memdb/help'), 'MemberDB');

	if(file_exists($include)) {
		if($page!='index') $heading = sprintf('%s - %s', $heading, humanize($page));
	}
	else {
		$view = "memdb/help/index";
	}
		
	$this->data['heading'] = $heading;
	return view($view, $this->data);
}

public function enrol($key='') {
	$this->data['title'] = 'Join Hawth';
	$this->data['heading'] = 'Join Hawth GC';
		
	$appvars = new \App\Models\Appvars();
	foreach(['invites', 'ethnics', 'groups'] as $varname) {
		$appvar = $appvars->find("memdb.{$varname}");
		$this->data[$varname] = $appvar ? $appvar->value : [];
	}
	
	if(!$key) return view('memdb/enrol/error', $this->data);
	if(empty($this->data['invites'][$key])) return view('memdb/enrol/error', $this->data);
	$this->data['invite'] = $this->data['invites'][$key];
	
	$keys = ['name1', 'name2', 'gender', 'DoB', 'ethnic', 'SpecialNeeds', 'MedicalNotes', 'MemberNotes', 'BadgeNotes', 'DoJ', 'contact_name', 'contact_rel', 'contact_con', 'Accountname1', 'Accountname2', 'Address', 'contact_0_name', 'contact_0_rel', 'contact_0_con', 'contact_1_name', 'contact_1_rel', 'contact_1_con', 'contact_2_name', 'contact_2_rel', 'contact_2_con', 'contact_3_name', 'contact_3_rel', 'contact_3_con', 'AccountSearch1', 'AccountSearch2', 'pay_method', 'agree0', 'agree1', 'com_email', 'com_sms', 'com_mail', 'chk_medical', 'con_participant', 'con_parent', 'con_welfare']; 	
	foreach($keys as $key) {
		$val = strval($this->request->getPost($key));
		if($val==='' && isset($this->data['invite'][$key])) $val = strval($this->data['invite'][$key]);
		$this->data['postvar'][$key] = $val;
	}
	
	$this->data['group'] = sprintf('%s group, starting at %s', '{group name}', $this->data['groups'][$this->data['invite']['group']]);
		
	return view("memdb/enrol/index", $this->data);
}

public function email($key='') {
	if(!\App\Libraries\Auth::check_role('admin')) {
		throw new \RuntimeException("Login required for MemDB function", 401);
	}
		
	$appvars = new \App\Models\Appvars();
	foreach(['invites', 'groups'] as $varname) {
		$appvar = $appvars->find("memdb.{$varname}");
		$this->data[$varname] = $appvar ? $appvar->value : [];
	}
	if(!isset($this->data['invites'][$key])) {
		return 'not found';
	}
	$this->data['postvar'] = $this->data['invites'][$key];
	$this->data['key'] = $key;
	$this->data['heading'] = 'Invitation to join email';
	return view("memdb/invite-done", $this->data);
}

public function invite() {
	if(!\App\Libraries\Auth::check_role('admin')) {
		throw new \RuntimeException("Login required for MemDB function", 401);
	}
	
	$appvars = new \App\Models\Appvars();
	foreach(['invites', 'groups'] as $varname) {
		$appvar = $appvars->find("memdb.{$varname}");
		$this->data[$varname] = $appvar ? $appvar->value : [];
	}
	// delete
	$del = $this->request->getPost('del');
	if($del) {
		unset($this->data['invites'][$del]);
		$appvar = new \App\Entities\Appvar;
		$appvar->id = "memdb.invites";
		$appvar->value = $this->data['invites'];
		$appvars->save_var($appvar);
	}
	// check POST
	$missing = 0;
	$this->data['postvar'] = [];
	$keys = ['name1', 'name2', 'email', 'group'];
	foreach($keys as $key) {
		$this->data['postvar'][$key] = strval($this->request->getPost($key));
		if(!$this->data['postvar'][$key]) $missing = 1;
	}
	// view
	if($missing) {
		$this->data['heading'] = 'Create invitation to join email';
		return view("memdb/invite", $this->data);
	}
	else {
		// create key
		$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$last = strlen($characters) - 1;
		do {
			$key = '';
			for ($i = 0; $i < 20; $i++) {
				$key .= $characters[rand(0, $last)];
			}
		} while(isset($this->data['invites'][$key]));
		$this->data['key'] = $key;
		// add invitation
		$this->data['invites'][$key] = $this->data['postvar'];
		$this->data['invites'][$key]['created'] = date('Y-m-d');
		$this->data['invites'][$key]['replied'] = null;
		$appvar = new \App\Entities\Appvar;
		$appvar->id = "memdb.invites";
		$appvar->value = $this->data['invites'];
		$appvars->save_var($appvar);
				
		$this->data['heading'] = 'New invitation to join email';
		return view("memdb/invite-done", $this->data);
	}
}

public function waiting($section='recreational') {
	$this->data['heading'] = 'Pre-school waiting list';
	$this->data['intro'] = 'Children joining this list must be born after 31/08/2017. Older children will not be accepted.';

	return view("memdb/waiting/pre-school", $this->data);

}

}
