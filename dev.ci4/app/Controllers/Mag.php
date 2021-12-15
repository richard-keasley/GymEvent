<?php namespace App\Controllers;

class Mag extends \App\Controllers\BaseController {

public function __construct() {
	$this->data['breadcrumbs'][] = ['mag', "Men's Artistic"];
	$this->data['title'] = "Men's Artistic";
	$this->data['heading'] = "Men's Artistic";
}
	
public function index() {
	$this->data['index'] = \App\Libraries\Mag\Rules::index();
	return view('mag/index', $this->data);
}

public function rules($setname = null) {
	$this->data['ruleset'] = \App\Libraries\Mag\Rules::load($setname);
	if(!$this->data['ruleset']) {
		throw new \RuntimeException("Can't find MAG rule set $setname", 404);
	}
	$this->data['breadcrumbs'][] = ["mag/rules/{$setname}", $this->data['ruleset']->title];
	$this->data['title'] = $this->data['ruleset']->title;
	$this->data['heading'] = $this->data['ruleset']->title;
	return view('mag/rules', $this->data);
}

public function routine() {
	$getPost = $this->request->getPost();
	$cmd = $this->request->getPost('cmd');
		
	if($getPost) {
		// sanitize
		$search = ['<','>','&'];
		$replace = ['{','}','+'];
		foreach($getPost as $key=>$val) {
			$getPost[$key] = trim(str_replace($search, $replace, $val));
		}
		if($cmd) $getPost['saved'] = date('Y-m-d H:i:s');
	}
		
	$this->data['exeset'] = new \App\Libraries\Mag\Exeset($getPost);
	$this->data['title'] = $this->data['exeset']->name ? $this->data['exeset']->name  : 'Routine sheet';
	$this->data['heading'] = $this->data['title'];
	
	$this->data['breadcrumbs'][] = ['mag/routine', "Routine sheet"];
	
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
		return view('mag/exeset/edit', $this->data);
	}
}

} 
