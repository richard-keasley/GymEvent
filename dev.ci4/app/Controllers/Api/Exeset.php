<?php namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;

class Exeset extends \App\Controllers\BaseController {
	
use ResponseTrait;

public function index() {
	return $this->respondNoContent();
}

public function exeval() {
	$request = $this->request->getPost();
	$request['saved'] = date('Y-m-d H:i:s');
	
	$exeset = new \App\Libraries\Rulesets\Exeset($request);

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
		$response['html'][$exekey] = \view('exeset/exeval', $viewdata);
	}
	
	return $this->respond($response);
}

public function view() {
	$request = $this->request->getPost();
	
	$layout = $request['layout'] ?? 'default' ;
	$pattern = 'exeset/view-%s';
	$viewname = sprintf($pattern, $layout);
	$include = config('Paths')->viewDirectory . "/{$viewname}.php";
	if(!file_exists($include)) {
		$layout = 'default';
		$viewname = sprintf($pattern, $layout);
	}
	# return $viewname;
	
	$data = [
		'exeset' => new \App\Libraries\Rulesets\Exeset($request),
	];
	
	$html = \view($viewname, $data);
	return $this->respond($html);
}

}
