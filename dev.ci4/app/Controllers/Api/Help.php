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
	if(!$html) {
		$message = "Can't find help for {$id}";
		return $this->failNotFound($message);
	}
	
	$allowed = \App\Libraries\Auth::check_path($html->path);
	if($allowed) {
		$response = [
			'heading' => $html->heading,
			'body' => $html->value,
		];	
		return $this->respond($response);
	}
	
	if(session('user_id')) {
		$message = "You do not have permission to view this";
		return $this->failForbidden($message);
	}
	else {
		$message = "You need to be logged in to view this";
		return $this->failUnauthorized($message);
	}	
}

}
