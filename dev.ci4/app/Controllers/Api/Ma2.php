<?php namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;

class Ma2 extends \App\Controllers\BaseController {
	
use ResponseTrait;

public function index() {
	return $this->respondNoContent();
}

public function exeval() {
	$request = $this->request->getPost();
	$request['saved'] = date('Y-m-d H:i:s');
	
	$exeset = new \App\Libraries\Ma2\Exeset($request);

	$response = [
		# 'server' => $_SERVER,
		# 'request' => $_REQUEST,
		'data' => $exeset->export(),
		'html' => []
	];
	
	// add in HTML
	$viewdata = [
		'exeset' => $exeset,
		'exekey' => ''
	];
	foreach($exeset->exercises as $exekey=>$exercise) {
		$viewdata['exekey'] = $exekey;
		$response['html'][$exekey] = \view('ma2/exeset/exeval', $viewdata);
	}
	
	return $this->respond($response);
}

public function print() {
	$request = $this->request->getPost();
	$data = [
		'exeset' => new \App\Libraries\Ma2\Exeset($request)
	];
	$html = view('ma2/exeset/print-exeset', $data);
	return $this->respond($html);
}

}
