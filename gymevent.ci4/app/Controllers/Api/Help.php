<?php namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;

class Help extends \App\Controllers\BaseController {
	
use ResponseTrait;
	
public function index() {
	return $this->respondNoContent();
}

public function view(...$segments) {
	if(!$segments) $segments = ['index'];
	$stub = implode('/', $segments);
	if(!\App\Libraries\Auth::check_path($stub)) return $this->error('Access denied');
			
	$viewname = "help/{$stub}";
	$include = VIEWPATH . "{$viewname}.php";
	if(!file_exists($include)) return $this->error("Can't find help for {$stub}");
	return $this->respond(view($viewname));
}

public function error($msg) {
	return sprintf('<p class="p-3 alert-danger">%s!</p>', $msg);
}

}