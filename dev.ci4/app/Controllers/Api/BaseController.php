<?php 
namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;

class BaseController extends \App\Controllers\BaseController {

protected function error($msg) {
		d($this->respond($msg));
		die;

	
	
	
	die(sprintf('<p class="p-3 alert-danger">%s!</p>', $msg));
}

protected function check_path() {
	$segments = current_url(true)->getSegments();
	$arr = []; $start = false;
	foreach($segments as $segment) {
		if($start) $arr[] = $segment;
		if($segment=='api') $start = true;
	}
	return \App\Libraries\Auth::check_path(implode('/', $arr));
}

}
