<?php namespace App\Controllers;

class Ma2 extends \App\Controllers\BaseController {

public function __construct() {
	helper('html');
	$this->data['breadcrumbs'][] = ['ma2', "Men's Artistic"];
	$this->data['title'] = "Men's Artistic";
	$this->data['heading'] = "Men's Artistic";
	$this->data['filename'] = "mag_routines";
	$this->data['head'] = '';
/*
ToDo
'<link rel="manifest" href="/app/mag/webmanifest.json">
<meta name="apple-mobile-web-app-title" content="MAG routines">';
*/
}
	
public function index() {
	$this->data['index'] = \App\Libraries\Mag\Rules::index;
	return view('mag/index', $this->data);
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
	return view('mag/rules', $this->data);
}

public function routineSW() {
	// service worker
	$this->response->setHeader('Content-Type', 'application/javascript');
	return view('ma2/exeset/sw', $this->data);
}

public function routine($layout=null) {
	
	$this->data['upload'] = null;
	$file = $this->request->getFile('upload');
	if($file) {
		if($file->isValid()) {
			$json = file_get_contents($file->getPathname());
			$upload = \App\Libraries\Ma2\Exeset::read_json($json);
			if($upload['error']) {
				$this->data['messages'][] = $upload['error'];
			}
			else {
				$this->data['upload'] = $upload;
				$this->data['upload']['file'] = $file;
			}
			
		}
		else {
			$this->data['messages'][] = "Upload: {$file->getErrorString()}";
		}
	}
	
	$layouts = ['edit', 'print'];
	if(!in_array($layout, $layouts)) $layout = 'edit';
	
	$config = new \config\paths;
	$css = "{$config->viewDirectory}/ma2/exeset/{$layout}.css";
	$minifier = new \MatthiasMullie\Minify\CSS($css);
	$this->data['head'] .= sprintf('<style>%s</style>', $minifier->minify());
	
	$this->data['title'] = 'MAG routines';
	$this->data['heading'] = 'MAG routine sheets';
	$this->data['breadcrumbs'][] = ['ma2/routine', "Routine sheets"];
		
	return view("ma2/exeset/{$layout}", $this->data);
}

public function export() {
	$json = $this->request->getPost('exesets');
	$arr = json_decode($json, true);
	
	$export = [];
	foreach($arr as $request) {
		$exeset = new \App\Libraries\Ma2\Exeset($request);
		$export[] = $exeset->export();
	}
	return $this->download($export, null, $this->data['filename'], 'json');
}

}