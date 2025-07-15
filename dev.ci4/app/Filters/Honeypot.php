<?php

/**
 * copied from 
 * CI/system/filters/honeypot
 */

# namespace CodeIgniter\Filters;
namespace App\Filters;

use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

# use CodeIgniter\Honeypot\Exceptions\HoneypotException;
use App\Exceptions\Exception as HoneypotException;

class Honeypot extends \CodeIgniter\Filters\Honeypot {
 
public function before(RequestInterface $request, $arguments = null) {
	if(!$request instanceof IncomingRequest) {
		return;
	}
	
	# return; // disable honeypot for testing
	
	$getPost = $request->getPost();
	if(!$getPost) return; // nothing posted 
	
	$config = config('Honeypot');
	$posted = $getPost[ $config->name ] ?? null ; 
	if($posted===null) {
		throw HoneypotException::honeypot("Honeypot not set");
	}
	if($posted) {
		throw HoneypotException::honeypot("Honeypot completed");
	}
	
	/*
	if(Services::honeypot()->hasContent($request)) {
		throw HoneypotException::isBot();
	}
	*/
}

public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {
	parent::after($request, $response, $arguments);
	
	/*
	stop honeypot attaching to GET forms
	ensure it runs after honeypot!
	*/
	$body = $response->getBody();
	if(!$body) return;
	
	$translate = [
		'<getform' => '<form method="GET"',
		'</getform>' => '</form>',
	];
	$body = strtr($body, $translate);
	$response->setBody($body);
    
	return null;
}

/*
exceptions do not return
no honeypot `after` filter will be applied to response
this hack ensures honeypot field is entered for error_401
ToDo: convert login form from 401 to normal view
*/
static function template() : string {
	// is filter active for this request
	$found = false;
	foreach(service('filters')->getFilters() as $arr) {
		if(in_array('honeypot', $arr)) $found = true;
		if(isset($arr['honeypot'])) $found = true;
	}
	if(!$found) return '';
		
	// copied from CodeIgniter\Honeypot\honeypot::preparetemplate()
	$config = config('Honeypot');
	$template = $config->template;
	$template = str_ireplace('{label}', $config->label, $template);
	$template = str_ireplace('{name}', $config->name, $template);
	if($config->hidden) {
		$template = str_ireplace('{template}', $template, $config->container);
	}
	return $template;
}

}
