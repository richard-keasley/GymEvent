<?php namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;

class Help extends \App\Controllers\BaseController {
	
use ResponseTrait;
	
public function index() {
	return $this->respondNoContent();
}

public function view($id=0) {
	$htmls = new \App\Models\Htmls;
	$html = $htmls->find($id);
	if(!$html) return $this->error("Can't find help for {$id}");;
	return $this->respond($html->value);
}

public function error($msg='error') {
	return sprintf('<p class="alert alert-danger">%s!</p>', $msg);
}

}
