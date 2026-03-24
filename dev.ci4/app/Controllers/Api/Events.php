<?php namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;

class Events extends \App\Controllers\BaseController {
	
use ResponseTrait;

private function find($event_id) {
	$event_id = (int) $event_id;
	return model('\App\Models\Events')->find($event_id);
}
	
public function index() {
	return $this->respondNoContent();
}

public function password($event_id=0) {
	$event = $this->find($event_id);
	if(!$event) {
		$message = "Can't find event {$event_id}";
		return $this->failNotFound($message);
	}
	
	$password = $this->request->getPost('password') ?? '';
	$success = $password===$event->password;
	return $this->respond($success);
}

}
