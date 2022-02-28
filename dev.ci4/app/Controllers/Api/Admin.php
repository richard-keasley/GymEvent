<?php namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;

class Admin extends \App\Controllers\BaseController {
	
use ResponseTrait;
	
public function index() {
	return $this->respondNoContent();
}

}
