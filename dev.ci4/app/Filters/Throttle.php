<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Throttle implements FilterInterface {
/**
 * copied from
 * https://codeigniter.com/user_guide/libraries/throttler.html
 */
 
public function before(RequestInterface $request, $arguments=null) {
	$throttler = service('throttler');	

	// Restrict an IP address to no more than 1 request
	// per second across the entire site.
	$success = $throttler->check($request->getIPAddress(), 20, 60);
	return $success ? 
		null : 
		service('response')->setStatusCode(429) ;
}

public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {
    // nothing to do here
}


}