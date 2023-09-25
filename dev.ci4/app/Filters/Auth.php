<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class Auth implements FilterInterface {
	
/*
check /app/config/filters to see when these filters will be run
https://codeigniter4.github.io/userguide/incoming/filters.html
*/

public function before(RequestInterface $request, $arguments = null) {
	\App\Libraries\Auth::init();
	
	$segments = $request->getUri()->getSegments();
	$request_path = implode('/', $segments);
	# $request_path = $request->uri->getPath();

	$messages = [];
	
	$check_ip = \App\Libraries\Auth::$lgn_model->check_ip($request->getIPAddress());
	if(!$check_ip) {
		self::die_nice($request_path, 'Oops! Overuse injury', 423);
	}
	
	// check for existing login / logout
	if($request->getPost('logout')) {
		\App\Libraries\Auth::logout();
	}
	else {
		\App\Libraries\Auth::check_login();
	}
	
	// check for new login
	$postUser = \App\Libraries\Auth::login_request($request->getPost());
	if($postUser) {
		switch($postUser->login) {
			case 'login':
			if(!\App\Libraries\Auth::login($postUser->name, $postUser->password)) {
				$messages[] = 'Username or Password is wrong';
			}
			break;
			
			case 'new';
			if($postUser->password2!==$postUser->password) $messages[] = 'Passwords do not match';
			if(!$messages) {
				$postUser->role = 'club';
				$user_id = \App\Libraries\Auth::$usr_model->insert($postUser);
				if($user_id) {
					$messages[] = ["Created new user", 'success'];
					\App\Libraries\Auth::loginas($user_id, 'created');
					\App\Libraries\Auth::$lgn_model->insert(['user_id'=>$user_id]);
				}
				else {
					$messages = \App\Libraries\Auth::$usr_model->errors();
				}
			}
			break;	
		}
	}
	
	// if there is a login, check this role is enabled
	$user_role = $_SESSION['user_role'] ?? null;
	if($user_role) {
		$min_role = \App\Libraries\Auth::$min_role;
		if(!\App\Libraries\Auth::check_role($min_role)) {
			$messages[] = "{$user_role} not allowed";
			\App\Libraries\Auth::logout();
		}
	}
	
	if($messages) {	
		$session = \Config\Services::session();
		$session->setFlashdata('messages', $messages);
	}
		
	// check permissions
	$allowed = \App\Libraries\Auth::check_path($request_path);
	if($allowed) return;

	/* access denied */
	$disabled = \App\Libraries\Auth::check_path($request_path, 0)=='disabled';
	if($disabled) {
		$message = "Service unavailable"; 
		$code = 423;
	}
	else {
		if(session('user_id')) {
			$message = "You do not have permission to view this page";
			$code = 403;
		}
		else {
			$message = "You need to be logged in to view this page";
			$code = 401;
		}
	}
	self::die_nice($request_path, $message, $code);
}

public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {
        // Do something here
}

static function die_nice($request_path, $message='Application error', $code=500) {
	$content_type = strpos($request_path, 'api/')===0 ? 'json' : 'html' ;
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
		throw \App\Exceptions\Exception::exception($message, $code);
	}
	$response->send();
	die;	
}

}
