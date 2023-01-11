<?php namespace App\Controllers;

class Mag extends \App\Controllers\BaseController {

public function __construct() {
	$this->data['breadcrumbs'][] = ['mag', "Men's Artistic"];
	$this->data['title'] = "Men's Artistic";
	$this->data['heading'] = "Men's Artistic";
	$this->data['head'] = '
<link rel="manifest" href="/app/mag/webmanifest.json">
<meta name="apple-mobile-web-app-title" content="MAG routines">';
}
	
public function index() {
	$this->data['index'] = \App\Libraries\Mag\Rules::index;
	return view('mag/index', $this->data);
}

public function rules($rulesetname = null) {
	if(!\App\Libraries\Mag\Rules::exists($rulesetname)) {
		throw new \RuntimeException("Can't find MAG rule set $rulesetname", 404);
	}
	
	$this->data['index'] = \App\Libraries\Mag\Rules::index;
	$this->data['ruleset'] = \App\Libraries\Mag\Rules::load($rulesetname);
	
	$this->data['breadcrumbs'][] = ["mag/rules/{$rulesetname}", $this->data['ruleset']->title];
	$this->data['rulesetname'] = $rulesetname;
	$this->data['title'] = $this->data['ruleset']->title;
	$this->data['heading'] = $this->data['ruleset']->title;
	return view('mag/rules', $this->data);
}

public function routineSW() {
	// service worker
	$this->response->setHeader('Content-Type', 'application/javascript');
	return view('mag/exeset/sw', $this->data);
}

public function routine($name='') {
	$getPost = $this->request->getPost();
	$this->data['exeset'] = new \App\Libraries\Mag\Exeset($getPost);
	$gymnast_name = $this->data['exeset']->name ? 
		$this->data['exeset']->name : 
		humanize($name);
	if($gymnast_name) {
		$this->data['title'] = $gymnast_name;
		$this->data['heading'] = $gymnast_name; 
	}
	else {
		$this->data['title'] = $this->data['exeset']->ruleset->title;
		$this->data['heading'] = $this->data['exeset']->ruleset->description;
	}
	$this->data['breadcrumbs'][] = ['mag/routine', "Routine sheet"];
	
	$cmd = $this->request->getPost('cmd');		
	switch($cmd) {
		case 'print':
		case 'store':
		$getPost['cmd'] = 'edit';
		$this->data['post'] = $getPost;
		if($cmd=='print') return view('mag/exeset/print', $this->data);
		// store
		$filename = preg_replace('#[^a-z_ ]#i', '', $this->data['exeset']->name);
		$filename = str_replace(' ', '_', $filename);
		if(!$filename) $filename = 'routine';
		$this->response->setHeader('Content-Disposition', "attachment; filename={$filename}.html");
		return view('mag/exeset/print', $this->data);
		
		default:
		$this->data['head'] .= '<link rel="stylesheet" type="text/css" href="/app/mag/exeset-edit.css">';
		return view('mag/exeset/edit', $this->data);
	}
}

} 
