<?php namespace App\Controllers;

use CodeIgniter\Controller;

class User extends \App\Controllers\BaseController {
	
function __construct() {
	$this->usr_model = new \App\Models\Users();
	$this->data['breadcrumbs'][] = 'user';
	$user_id = intval(session('user_id'));
	// compare to /admin/users/find
	$this->data['user'] = $this->usr_model->find($user_id);
	if(!$this->data['user']) {
		$message = "Can't find user {$user_id}";
		\App\Libraries\Exception::not_found($this->request, $message);
	}
	
	$this->data['user_id'] = $user_id;
	$this->data['user_self'] = $this->data['user']->self();
}

public function index() {
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
	if($this->request->getPost('save')) {
		$postUser = $this->request->getPost();
		if($this->data['user']->self()) {
			foreach(['role'] as $key) {
				unset($postUser[$key]);
			}
		}
		$postUser['id'] = $this->data['user_id'];
		
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
			$this->data['user'] = $this->usr_model->find($this->data['user_id']);
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
