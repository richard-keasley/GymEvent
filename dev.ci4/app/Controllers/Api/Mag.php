<?php namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;

class Mag extends \App\Controllers\BaseController {
	
use ResponseTrait;

public function index() {
	return $this->respondNoContent();
}

public function exevals() {
	$request = $this->request->getGet();
	$exedata = []; // exercise set from request
	$response = []; // response (exeval for each exercise)
		
	$request['saved'] = date('Y-m-d H:i:s');
	$exedata['exeset'] = new \App\Libraries\Mag\Exeset($request);

	foreach($exedata['exeset']->exercises as $exekey=>$exercise) {
		$exedata['exekey'] = $exekey;
		$response[$exekey] = \view('mag/exeset/exeval', $exedata);
	}
	return $this->respond($response);
}

}
