<?php namespace App\Controllers;

use CodeIgniter\Controller;

class User extends \App\Controllers\BaseController {
	
function __construct() {
	$this->usr_model = new \App\Models\Users();
	$this->data['breadcrumbs'][] = 'user';
}

public function index() {
	$user_id = session('user_id');
	$this->data['user'] = $this->usr_model->find($user_id);
	if(!$this->data['user']) throw new \RuntimeException("Can't find user $user_id", 404);
	
	// view
	$this->data['toolbar'] = [getlink('user/edit')];
	
	$this->data['toolbar'] = [
		\App\Libraries\View::back_link(''),
		getlink('user/edit'),
		getlink('admin/users', 'admin')
	];
	return view('users/view', $this->data);
}

public function edit() {
	// compare to /admin/users/edit
	$user_id = session('user_id');
	$this->data['user'] = $this->usr_model->find($user_id);
	if(!$this->data['user']) throw new \RuntimeException("Can't find user $user_id", 404);
				
	if($this->request->getPost('save')) {
		$postUser = $this->request->getPost();
		if($this->data['user']->self()) {
			foreach(['role'] as $key) {
				unset($postUser[$key]);
			}
		}
		$postUser['id'] = $user_id;
		
		$errors = '';
		if($postUser['password']!=$postUser['password2']) {
			$errors = ['Passwords do not match'];
		}
		if(!$errors) {
			if(!$postUser['password']) unset($postUser['password']);
			// update 
			if($this->usr_model->save($postUser)) {
				$this->data['messages'][] = ["Your information has been updated", 'success'];
			}
			else {
				$errors = $this->usr_model->errors();
			}
		}
		if($errors) {
			$this->data['user'] = new \App\Entities\User($postUser);
			$this->data['messages'] = $errors;
		}
		else {
			$this->data['user'] = $this->usr_model->find($user_id);
		}
	}
	// view
	$this->data['toolbar'] = [
		\App\Libraries\View::back_link('user')
	];
	$this->data['breadcrumbs'][] = ["user/edit", 'edit'];
	return view('users/edit', $this->data);
}

} 
