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
	$messages = [];
	\App\Libraries\Auth::init();
	
	$check_ip = \App\Libraries\Auth::$lgn_model->check_ip($request->getIPAddress());
	if(!$check_ip) throw new \RuntimeException('Oops! Overuse injury', 423);
	
	// check for login / logout
	if($request->getPost('logout')) {
		\App\Libraries\Auth::logout();
	}
	else {
		\App\Libraries\Auth::check_login();
	}
	switch($request->getPost('login')) {
		case 'login':
		$name = $request->getPost('name');
		$password = $request->getPost('password');
		if(!\App\Libraries\Auth::login($name, $password)) {
			$messages = ['Username or Password is wrong'];
		}
		break;
		
		case 'new';
		$postUser = new \App\Entities\User($request->getPost());
		if($postUser->password2!==$postUser->password) $messages = ['Passwords do not match'];
		if(!$messages) {
			$postUser->role = 'club';
			$user_id = \App\Libraries\Auth::$usr_model->insert($postUser);
			if($user_id) {
				$messages = [["Created new user", 'success']];
				\App\Libraries\Auth::loginas($user_id, 'created');
				\App\Libraries\Auth::$lgn_model->insert(['user_id'=>$user_id]);
			}
			else {
				$messages = \App\Libraries\Auth::$usr_model->errors();
			}
		}
		break;
	}
	
	// check permissions
	$path = $request->uri->getPath();
	$allowed = \App\Libraries\Auth::check_path($path);
	$disabled = \App\Libraries\Auth::$check_paths[$path][0]=='disabled';
	
	if($allowed) {
		// superuser is allowed to view disabled controllers
		if($disabled) $messages[] = 'This service is closed';
		if($messages) {	
			$session = \Config\Services::session();
			$session->setFlashdata('messages', $messages);
		}
		return;
	}
	
	/* access denied */
	if($disabled) throw new \RuntimeException("Service unavailable", 423);
	if(session('user_id')) throw new \RuntimeException("You do not have permission to view this page", 403);
	throw new \RuntimeException("You need to be logged in to view this page", 401);
}

public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {
        // Do something here
}
}