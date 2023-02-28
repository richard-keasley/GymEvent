<?php namespace App\Libraries;

class Exception {

static function die_nice($request, $message='Application error', $code=500) {
	$request_path = $request->uri->getPath();
	$content_type = strpos($request_path, 'api/')===0 ? 'json' : 'html' ;
	
	$paths = config('Paths');
	$app = config('App');
	$data = [
		'message' => $message,
		'code' => $code
	];
	
	$response = new \CodeIgniter\HTTP\Response($app);
	$response->setStatusCode($code, $message);
	
	switch($content_type) {
		case 'json' : 
		$response->setJSON($data); 
		break;
		
		case 'xml' : 
		$response->setXML($data); 
		break;
		
		case 'html' :
		default:
		$view = "/errors/html/error_{$code}";
		$viewfile = realpath($paths->viewDirectory . "{$view}.php");
		$data['request'] = $request;
		if(!$viewfile) {
			throw new \RuntimeException($message, $code);
		}
		$response->setBody(view($view, $data));
	}
	$response->send();
	die;	
}

static function not_found($request, $message='Not found') {
	self::die_nice($request, $message, 404);
}

}
