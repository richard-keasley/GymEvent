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
		'data' => $exedata['exeset']->data,
		'html' => []
	];
	# $response['data']['exercises'] = $exedata['exeset']->exercises;
		
	foreach($exedata['exeset']->exercises as $exekey=>$exercise) {
		$exedata['exekey'] = $exekey;
		$response['html'][$exekey] = \view('mag/exeset/exeval', $exedata);
		$response['data'][$exekey] = $exercise;
	}
	return $this->respond($response);
}

}
