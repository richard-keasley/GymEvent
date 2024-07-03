<?php namespace App\Controllers;

class Ma2 extends \App\Controllers\BaseController {

public function __construct() {
	$this->data['breadcrumbs'][] = ['ma2', "Men's Artistic"];
	$this->data['title'] = "Men's Artistic";
	$this->data['heading'] = "Men's Artistic";
	$this->data['head'] = '
<link rel="manifest" href="/app/ma2/webmanifest.json">
<meta name="apple-mobile-web-app-title" content="MAG routines">';
}
	
public function index() {
	$this->data['index'] = \App\Libraries\Mag\Rules::index;
	return view('ma2/index', $this->data);
}

public function rules($rulesetname = null) {
	if(!\App\Libraries\Mag\Rules::exists($rulesetname)) {
		$message = "Can't find MAG rule set {$rulesetname}";
		throw \App\Exceptions\Exception::not_found($message);
	}
	
	$this->data['index'] = \App\Libraries\Mag\Rules::index;
	$this->data['ruleset'] = \App\Libraries\Mag\Rules::load($rulesetname);
	
	$this->data['breadcrumbs'][] = ["ma2/rules/{$rulesetname}", $this->data['ruleset']->title];
	$this->data['rulesetname'] = $rulesetname;
	$this->data['title'] = $this->data['ruleset']->title;
	$this->data['heading'] = $this->data['ruleset']->title;
	return view('ma2/rules', $this->data);
}

public function routineSW() {
	// service worker
	$this->response->setHeader('Content-Type', 'application/javascript');
	return view('ma2/exeset/sw', $this->data);
}

public function routine($rulesetname='') {
	
	$data = ['rulesetname'=>$rulesetname];
	$this->data['exeset'] = new \App\Libraries\Mag\Exeset($data);
	
	// probably no post here 
	$getPost = $this->request->getPost();		
	// $this->data['exeset'] = new \App\Libraries\Mag\Exeset($getPost);
	
	$this->data['title'] = $this->data['exeset']->ruleset->title;
	$this->data['heading'] = $this->data['exeset']->ruleset->description;
	
	
	$this->data['breadcrumbs'][] = ['ma2/routine', "Routine sheet"];
	
	$cmd = $this->request->getPost('cmd');		
	switch($cmd) {
		case 'print':
		case 'store':
		$getPost['cmd'] = 'edit';
		$this->data['post'] = $getPost;
		if($cmd=='print') return view('ma2/exeset/print', $this->data);
		// store
		$filename = preg_replace('#[^a-z_ ]#i', '', $this->data['exeset']->name);
		$filename = str_replace(' ', '_', $filename);
		if(!$filename) $filename = 'routine';
		$this->response->setHeader('Content-Disposition', "attachment; filename={$filename}.html");
		return view('ma2/exeset/print', $this->data);
		
		default:
		$this->data['head'] .= '<link rel="stylesheet" type="text/css" href="/app/mag/exeset-edit.css">';
		return view('ma2/exeset/edit', $this->data);
	}
}

} 
