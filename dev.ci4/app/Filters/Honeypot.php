<?php

/**
 * copied from 
 * CI/system/filters/honeypot
 */

# namespace CodeIgniter\Filters;
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

use CodeIgniter\Honeypot\Exceptions\HoneypotException;


/**
 * Honeypot filter
 *
 * @see \CodeIgniter\Filters\HoneypotTest
 */
class Honeypot implements FilterInterface {
/**
 * Checks if Honeypot field is empty, if not then the
 * requester is a bot
 *
 * @param array|null $arguments
 *
 * @throws HoneypotException
 */
 
public function before(RequestInterface $request, $arguments = null) {
	if(!$request instanceof IncomingRequest) {
		return;
	}
	if(Services::honeypot()->hasContent($request)) {
		# throw HoneypotException::isBot();
		throw \App\Exceptions\Exception::honeypot("Honeypot filled on page load");
	}
}

/**
 * Attach a honeypot to the current response.
 *
 * @param array|null $arguments
 */
public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {
	Services::honeypot()->attachHoneypot($response);
}

static function template() : string {
	// exceptions do not return
	// no honeypot filter will run after response
	// this hack ensures honeypot is entered for error_401
	
	$filters = config('Filters')->globals['after'];
	$index = array_search('honeypot', $filters);
	if($index===false) return ''; // honeypot not requested
	
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
