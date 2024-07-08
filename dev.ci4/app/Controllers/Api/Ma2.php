<?php namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;

class Ma2 extends \App\Controllers\BaseController {
	
use ResponseTrait;

public function index() {
	return $this->respondNoContent();
}

public function exeval() {
	$request = $this->request->getGet();
	
	$exedata = []; // exercise set from request
		
	$request['saved'] = date('Y-m-d H:i:s');
	$exedata['exeset'] = new \App\Libraries\Ma2\Exeset($request);

	// response (exeval for each exercise)
	$response = [
		# 'server' => $_SERVER,
		# 'request' => $_REQUEST,
		'data' => $exedata['exeset']->data,	
		'html' => []
	];
	$response['data']['ruleset'] = [
		'name' => $exedata['exeset']->data['rulesetname'],
		'title' => $exedata['exeset']->ruleset->title,
		'description' => $exedata['exeset']->ruleset->description,
		'version' => $exedata['exeset']->ruleset->version
	];
	$dt = new \datetime($response['data']['ruleset']['version']);
	$response['data']['ruleset']['version'] = $dt->format('d F Y');
			
	foreach($exedata['exeset']->exercises as $exekey=>$exercise) {
		$exedata['exekey'] = $exekey;
		$response['html'][$exekey] = \view('ma2/exeset/exeval', $exedata);
		$response['data'][$exekey] = $exercise;
	}
	return $this->respond($response);
}

public function print() {
	$request = $this->request->getGet();
	$data = [
		'exeset' => new \App\Libraries\Ma2\Exeset($request)
	];
	$html = view('ma2/exeset/print-exeset', $data);
	return $this->respond($html);
}

}
