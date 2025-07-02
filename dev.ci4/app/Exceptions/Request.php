<?php

namespace App\Exceptions;

use \CodeIgniter\Exceptions;

class Request 
	extends \RuntimeException 
	implements Exceptions\ExceptionInterface, Exceptions\HTTPExceptionInterface 
	{

use Exceptions\DebugTraceableTrait;

function __construct(string $message = "", int $code = 400, ?Throwable $previous = null) {
	parent::__construct();
	
	$request = service('request');
	$uri = $request->getURI();
	$segments = $uri->getSegments();
	$path = $uri->getPath();
	$zone = $segments[0] ?? null ;
	$extension = pathinfo($path, PATHINFO_EXTENSION);
		
	if($zone=='api') {
		// all API responses are JSON
		$content_type = 'json';
	} 
	else {
		// guess from extension
		$mimes = config('Mimes');
		$arr = $mimes::$mimes[$extension] ?? [] ;
		
		$allowed = ['html', 'json', 'xml'];
		$content_type = $allowed[0]; // default
		foreach($arr as $val) {
			$val = explode('/', $val);
			$mime_type = $val[1] ?? '' ;
			if(in_array($mime_type, $allowed)) $content_type = $mime_type;
		}
	}
	# dd($zone, $extension, $content_type);
	
	if($content_type=='html') {
		throw \App\Exceptions\Exception::exception($message, $code);
	}
	
	// build custom response
	$data = [
		'message' => $message,
		'code' => $code,
	];
	$response = new \CodeIgniter\HTTP\Response(config('App'));
	$response->setStatusCode($code, $message);
	# dd($response);
	
	switch($content_type) {
		case 'json' : 
		$response->setJSON($data); 
		break;
		
		case 'xml' : 
		$response->setXML($data); 
		break;
		
		default:
		$response->setbody(print_r($data, 1));
	}
	$response->send();
	die;
}

}
