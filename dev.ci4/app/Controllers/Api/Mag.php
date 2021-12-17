<?php namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;

class Mag extends \App\Controllers\BaseController {
	
use ResponseTrait;
	
public function index() {
	return $this->respondNoContent();
}

public function exeval($exekey=null) {
	$data = $this->request->getGet();
	$data['saved'] = date('Y-m-d H:i:s');
	$this->data['exeset'] = new \App\Libraries\Mag\Exeset($data);

	$response = [];
	foreach($this->data['exeset']->exercises as $exekey=>$exercise) {
		$this->data['exekey'] = $exekey;
		$response[$exekey] = \view('mag/exeset/exeval', $this->data);
	}
	return $this->respond($response);
}

}
