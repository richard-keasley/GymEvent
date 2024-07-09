<?php namespace App\Controllers;

class Ma2 extends \App\Controllers\BaseController {

public function __construct() {
	helper('html');
	$this->data['breadcrumbs'][] = ['ma2', "Men's Artistic"];
	$this->data['title'] = "Men's Artistic";
	$this->data['heading'] = "Men's Artistic";
	$this->data['head'] = '
<link rel="manifest" href="/app/mag/webmanifest.json">
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

public function routine($layout=null) {
	$this->data['title'] = 'MAG routines';
	$this->data['heading'] = 'MAG routine sheets';
	$this->data['breadcrumbs'][] = ['ma2/routine', "Routine sheet"];
	
	$layouts = ['edit', 'print'];
	if(!in_array($layout, $layouts)) $layout = 'edit';
	
	$config = new \config\paths;
	$css = "{$config->viewDirectory}/ma2/exeset/{$layout}.css";
	$minifier = new \MatthiasMullie\Minify\CSS($css);
	$this->data['head'] .= sprintf('<style>%s</style>', $minifier->minify());
	
	return view("ma2/exeset/{$layout}", $this->data);



	// delete from here down


	
	$data = ['rulesetname'=>$rulesetname];
	$this->data['exeset'] = new \App\Libraries\Mag\Exeset($data);
	
	// probably no post here 
	$getPost = $this->request->getPost();		
	// $this->data['exeset'] = new \App\Libraries\Mag\Exeset($getPost);
	

	
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

public function export() {
	$json = $this->request->getPost('exesets');
	$arr = json_decode($json, true);
	
	$export = [];
	foreach($arr as $request) {
		$exeset = new \App\Libraries\Ma2\Exeset($request);
		$export[] = $exeset->export();
	}
	return $this->download($export, null, 'mag_routines', 'json');
}

public function import() {
	$this->data['exesets'] = [];
	$this->data['file'] = null;
	
	$file = $this->request->getFile('import');
	if($file) {
		$this->data['file'] = $file; 
		if($file->isValid()) {
			$json = file_get_contents($file->getPathname());		
			$this->data['exesets'] = $this->import_json($json);
		}
		else {
			$this->data['messages'][] = "Upload: {$file->getErrorString()}";
		}
	}
	
	$import = $this->request->getPost('import');
	if($import) {
		$json = $this->request->getPost('exesets');
		$exesets = $this->import_json($json);
		$this->data['messages'][] = "Import has not yet been done";
		# return redirect()->to('ma2/routine');
		d($exesets);
	}
		
	return view('ma2/exeset/import', $this->data);
}

private function import_json($json) {
	$exesets = [];
	try {
		# d($json);
		$flags = JSON_THROW_ON_ERROR;
		$arr = json_decode($json, true, 512, $flags);
		# d($arr);
		foreach($arr as $request) {
			$exesets[] = new \App\Libraries\Ma2\Exeset($request);
		}			
	}
	catch(\JsonException $ex) {
		$this->data['messages'][] = "{$ex->getMessage()} in uploaded file";
	}
	catch(\Exception $ex) {
		$this->data['messages'][] = $ex->getMessage();
	}
	return $exesets;
}

} 
