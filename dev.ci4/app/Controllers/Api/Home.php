<?php namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;

class Home extends \App\Controllers\BaseController {
	
use ResponseTrait;
	
public function index() {
	return $this->respondNoContent();
}

public function test($param=null, $format=null) {
	if($format) $this->format = $format;
	
	$status = intval($param);
	if($status>399) {
		$message = \App\Exceptions\Exception::get_reason($status);
		throw \App\Exceptions\Exception::exception($message, $status);
		
		$response = [
			'format' => $this->format,
			'error'  => \App\Exceptions\Exception::get_reason($status),
			'status' => $status,
		];
		return $this->fail($response, $status);
	}
	
	$response = [
		'format' => $this->format,
		'request' => [
			'method' => strtolower($this->request->getMethod()),
			'param' => $param
		],
		'post' => $this->request->getPost(),
		'get' => $this->request->getGet(),
	];
	return $this->respond($response);
}

}
