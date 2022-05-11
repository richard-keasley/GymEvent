<?php namespace App\Controllers;

use CodeIgniter\Controller;

class Reset extends \App\Controllers\BaseController {

function __construct() {
	$this->usr_model = new \App\Models\Users;
	$this->lgn_model = new \App\Models\Logins;
}

private function find($key, $value) {
	switch($key) {
		case 'email':
		return $this->usr_model->where($key, $value)->first();
	
		case 'key':
		$expiry = date('Y-m-d H:i:s', time() - 1800); // 30 minutes 
		return $this->usr_model
			->where('reset_time >', $expiry)
			->where('reset_key', $value)
			->first();
		
		case 'id':
		return $this->usr_model->find($value);
	}
	return null;
}

public function index() {
	$vw_index = 'users/reset/index';
	
	$this->data['title'] = 'Password reset';
	$this->data['heading'] = 'Password reset';

	$this->data['email'] = trim(strval($this->request->getPost('email')));
	if(!$this->data['email']) {
		$user_id = intval($this->request->getGet('user'));
		$user = $this->find('id', $user_id);
		if($user) $this->data['email'] = $user->email;
	}	
	
	if(!$this->data['email']) return view($vw_index, $this->data);
	if(!$this->request->getPost('reset')) return view($vw_index, $this->data);
	
	// lookup user
	$user = $this->find('email', $this->data['email']);
	if(!$user) {
		// no accounts use this email address
		$this->data['messages'] = ["Sorry! I don't know this email address!"];
		$this->lgn_model->insert(['error' => "Reset email not found ({$this->data['email']})"]);
		return view($vw_index, $this->data);
	}
	
	// build reset key 
	$reset_key = [];
	for($i=0; $i<3; $i++) {
		$bytes = random_bytes(2);
		$reset_key[] = bin2hex($bytes);
	}
	$reset_key = strtoupper(implode('-', $reset_key));
	$reset_time = date('Y-m-d H:i:s');
	
	// add reset key to user
	$user->reset_key = $reset_key;
	$user->reset_time = $reset_time;
	$this->usr_model->save($user);
	$this->lgn_model->insert(['error'=>'reset requested', 'user_id'=>$user->id]);

	// build message
	$this->data['user'] = $user; 
	$message = view('users/reset/email', $this->data);
	$email_to = ENVIRONMENT == 'production' ? $user->email : 'richard@base-camp.org.uk';

	// send email to user
	$email = \Config\Services::email();
	$email->setSubject('Password reset');
	$email->setMessage($message);
	$email->setBCC('richard@hawthgymnastics.co.uk');
	$email->setTo($email_to);
	# d($email);
	$email->send();
	
	// view
	$this->data['key'] = '';
	return view('users/reset/reset', $this->data);
}

public function reset($key='') {
	$vw_reset = 'users/reset/reset';
	$this->data['title'] = 'Password reset';
	$this->data['heading'] = 'Password reset';
		
	if(!$this->request->getPost('reset')) {
		// only process POST requests
		$this->data['key'] = $key;
		return view($vw_reset, $this->data);
	}
	// get key
	$key = $this->request->getPost('key');
	$this->data['key'] = $key;
	if(!$key) return view($vw_reset, $this->data);
	// find user
	$user = $this->find('key', $key);
	$this->data['user'] = $user; // remove this
	if(!$user) {
		$this->lgn_model->insert(['error'=>'wrong reset code']);
		$this->data['messages'][] = ['Sorry! Invalid code!', 'danger'];
		return view($vw_reset, $this->data);
	}
	// change password
	$user->password = $this->request->getPost('password');
	$success = $this->usr_model->save($user);
	if(!$success) {
		$this->data['messages'] = $this->usr_model->errors();
		return view($vw_reset, $this->data);
	}
	// login user
	$success = \App\Libraries\Auth::login($user->name, $user->password);
	if(!$success) {
		$this->data['messages'][] = ['Reset login failed!', 'danger'];
		return view($vw_reset, $this->data);
	}
	// remove key
	$user->reset_key = null;
	$user->reset_time = null;
	$success = $this->usr_model->save($user);
	$this->data['messages'][] = ['Password reset successful', 'success']; 
	return view('index', $this->data);
}

} 
