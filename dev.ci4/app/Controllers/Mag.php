<?php namespace App\Controllers;

class Mag extends \App\Controllers\BaseController {

public function __construct() {
	$this->data['back_link'] = 'mag';
	$this->data['breadcrumbs'][] = ['mag', "Men's Artistic"];
	$this->data['title'] = "Men's Artistic";
	$this->data['heading'] = "Men's Artistic";
	$this->data['filename'] = "mag_routines";
	$this->data['rule_options'] = \App\Libraries\Rulesets::options('mag');
}
	
public function index() {
	return view('mag/index', $this->data);
}

public function rules($rulesetname = null) {
	if(!\App\Libraries\Rulesets::exists($rulesetname)) {
		$message = "Can't find rule set {$rulesetname}";
		throw \App\Exceptions\Exception::not_found($message);
	}
	
	$this->data['ruleset'] = \App\Libraries\Rulesets::load($rulesetname);	
	$this->data['breadcrumbs'][] = ["mag/rules/{$rulesetname}", $this->data['ruleset']->title];
	$this->data['title'] = $this->data['ruleset']->title;
	$this->data['heading'] = $this->data['ruleset']->title;
	return view('rulesets/view', $this->data);
}

public function routine($viewname='', $layout='') {
	$file = $this->request->getFile('upload');
	$exelib = new \App\Libraries\Exeset;
	$data = $exelib->routine($viewname, $layout, $file);
	foreach($data as $key=>$val) {
		switch($key) {
			case 'messages':
			$this->data[$key][] = $val;
			break;
			
			default:
			$this->data[$key] = $val;
		}
	}
	
	$this->data['title'] = 'MAG routines';
	$this->data['heading'] = 'MAG routine sheets';
	$this->data['breadcrumbs'][] = ['mag/routine', "Routine sheets"];

	return view("exeset/{$this->data['viewname']}", $this->data);
}

public function export() {
	$json = $this->request->getPost('exesets');
	if(!$json) $json = '[]';
	$arr = json_decode($json, true);
	
	$export = [];
	foreach($arr as $request) {
		$exeset = new \App\Libraries\Rulesets\Exeset($request);
		$export[] = $exeset->export();
	}
	
	$filename = "{$this->data['filename']}.json";
	return $this->download($filename, $export);
}

}