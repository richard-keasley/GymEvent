<?php namespace App\Controllers;

class General extends \App\Controllers\BaseController {

public function __construct() {
	$this->data['back_link'] = 'general';
	$this->data['breadcrumbs'][] = ['general', "General Gymnastics"];
	$this->data['title'] = "General Gymnastics";
	$this->data['heading'] = "General Gymnastics";
	$this->data['filename'] = "fv_routines";
	$this->data['rule_options'] = \App\Libraries\Rulesets::options('general');
}
	
public function index() {
	return view('general/index', $this->data);
}

public function rules($rulesetname = null) {
	if(!\App\Libraries\Rulesets::exists($rulesetname)) {
		$message = "Can't find rule set {$rulesetname}";
		throw \App\Exceptions\Exception::not_found($message);
	}
	$this->data['ruleset'] = \App\Libraries\Rulesets::load($rulesetname);
	
	$this->data['breadcrumbs'][] = ["general/rules/{$rulesetname}", $this->data['ruleset']->title];
	$this->data['title'] = $this->data['ruleset']->title;
	$this->data['heading'] = $this->data['ruleset']->title;
	return view('rulesets/view', $this->data);
}

function skills($exekey='', $level='gold') {
	$rulesetname = 'Fv_' . strtolower($level);
	if(!isset($this->data['rule_options'][$rulesetname])) {
		$message = "Can't find {$rulesetname}";
		throw \App\Exceptions\Exception::not_found($message);
	}
	$ruleset = \App\Libraries\Rulesets::load($rulesetname);
	
	$exekey = strtoupper($exekey);
	$this->data['skills'] = $ruleset->skills($exekey);
	if(!$this->data['skills']) {
		$message = "Can't find {$exekey}";
		throw \App\Exceptions\Exception::not_found($message);
	}
	
	$title = humanize("{$level} " . ($this->data['skills']['name'] ?? 'skills')) ;
	$this->data['breadcrumbs'][] = ["general/skills/{$exekey}/{$level}", $title];
	
	$this->data['title'] = $title;
	$this->data['heading'] = "General: {$title}";
	$this->data['ruleset'] = $ruleset;
	$this->data['exekey'] = $exekey;
	return view('general/skills', $this->data);
}

public function videos() {
	$this->data['breadcrumbs'][] = ['general/videos', "Videos"];
	$this->data['heading'] = "F&amp;V videos";
	$this->data['title'] = "Videos";
	return view('general/videos', $this->data);
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
		
	$this->data['title'] = 'General routines';
	$this->data['heading'] = 'General gymnastics routine sheets';
	$this->data['breadcrumbs'][] = ['general/routine', "Routine sheets"];
	
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