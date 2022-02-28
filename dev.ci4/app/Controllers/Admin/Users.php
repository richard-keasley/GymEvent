<?php namespace App\Controllers\Admin;

class Users extends \App\Controllers\BaseController {

public function __construct() {
	$this->usr_model = new \App\Models\Users();
	$this->data['breadcrumbs'][] = 'admin';
	$this->data['breadcrumbs'][] = 'admin/users';
	$this->data['base_url'] = base_url(['admin', 'users']);
}

private function find($user_id) {
	$this->data['user'] = $this->usr_model->withDeleted()->find($user_id);
	if(!$this->data['user']) throw new \RuntimeException("Can't find user $user_id", 404);
	$this->data['user_self'] = $this->data['user']->self();
}
	
public function index() {
	// update
	$id = $this->request->getPost('enable');
	if($id) {
		$user = $this->usr_model->withDeleted()->find($id);
		$user->deleted_at = null;
		$this->usr_model->save($user);
	}
	$id = $this->request->getPost('disable');
	if($id) $this->usr_model->delete($id);
	// delete
	$id = $this->request->getPost('delete');
	if($id) $this->usr_model->delete_all($id);
		
	$update = $this->request->getPost('update');
	if($update=='club0') {
		$this->usr_model->where('role', 'club')->delete();
		$this->data['messages'][] = ["clubs disabled", 'success'];
	}
	if($update=='club1') {
		$this->usr_model->where('role', 'club')
			->set('deleted_at', null)
			->update();
		$this->data['messages'][] = ["clubs enabled", 'success'];
	}
	// read
	$filter_by = $this->request->getGet('by');
	$filter_val = $this->request->getGet('val');
		
	$this->usr_model->orderby('name');
	if($filter_by=='deleted') {
		if($filter_val) $this->usr_model->onlyDeleted();
	}
	else {
		$this->usr_model->withDeleted();
		if($filter_by) {
			$this->usr_model->where($filter_by, $filter_val);
		}
	}
	$this->data['users'] = $this->usr_model->findAll();
	return view('users/index', $this->data);
}

public function view($user_id=0) {
	$this->find($user_id);
	
	if(!$this->data['user_self']) {
		$set_enabled = $this->request->getPost('enable');
		if($set_enabled) {
			switch($set_enabled) {
				case 'delete': 
				$success = $this->usr_model->delete_all($user_id);
				if($success) return redirect()->to('admin/users');
				break;
				case 'enable':
				$this->data['user']->deleted_at = null;
				$this->usr_model->save($this->data['user']);
				break;
				case 'disable':
				$this->usr_model->delete($user_id);
				break;
			}
			$this->data['user'] = $this->usr_model->withDeleted()->find($user_id);
		}
		
		if($this->request->getPost('loginas') && \App\Libraries\Auth::check_role('superuser')) { 
			if(\App\Libraries\Auth::loginas($user_id)) {
				$this->data['user'] = $this->usr_model->find($user_id);
				$message = sprintf("Logged in as %s", $this->data['user']->name);
				$this->data['messages'][] = [$message, 'success'];
			}
			else $this->data['messages'][] = "Couldn't login as $user_id";
		}
	}
	
	// view
	$this->data['heading'] = $this->data['user']->name;
	$this->data['breadcrumbs'][] = ["admin/users/view/{$user_id}", $this->data['user']->name];
	
	$this->data['toolbar'] = [
		\App\Libraries\View::back_link('admin/users'),
		getlink("admin/users/edit/{$user_id}", 'edit'),
		sprintf('<a href="%s/%u" class="btn btn-outline-secondary">logins</a>', base_url('admin/users/logins/user_id'), $user_id)
	];
	
	if(!$this->data['user_self']) {
		if($this->data['user']->deleted_at) {
			$this->data['toolbar'][] = '<button name="enable"  value="enable" type="submit" title="enable" class="btn btn-success bi-check-circle"></button>';
			$this->data['toolbar'][] = '<button name="enable"  value="delete" type="submit" title="delete this user and all related data" class="btn btn-danger bi-trash"></button>';
		}
		else {
			$this->data['toolbar'][] = '<button name="enable" value="disable" type="submit" title="disable" class="btn bi-x-circle btn-danger"></button>';
			$this->data['toolbar'][] = getlink("admin/users/merge/{$user_id}", '<span class="bi-layer-backward" title="merge"></span>');			
		}
		if(\App\Libraries\Auth::check_path('superuser')) { 
			$this->data['toolbar'][] = '<button name="loginas" value="1" type="submit" class="btn btn-secondary">login as&hellip;</button>';
		}
	}
	return view('users/view', $this->data);
}

public function edit($user_id=0) {
	// compare to /user/edit
	$this->find($user_id);

	if(!\App\Libraries\Auth::check_role($this->data['user']->role)) throw new \RuntimeException("You can not edit this user", 403);
		
	if($this->request->getPost('save')) {
		$postUser = $this->request->getPost();
		$postUser['id'] = $user_id;
		if($this->data['user_self']) {
			$postUser['role'] = $this->data['user']->role;
		}
		
		$errors = '';
		if($postUser['password']!=$postUser['password2']) {
			$errors = ['Passwords do not match'];
		}
		if(!$errors) {
			if(!$postUser['password']) unset($postUser['password']);
			if(!\App\Libraries\Auth::check_role($postUser['role'])) unset($postUser['role']);
			// update 
			if($this->usr_model->save($postUser)) {
				$this->data['messages'][] = ["User details updated", 'success'];
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
			$this->data['user'] = $this->usr_model->withDeleted()->find($user_id);
		}
	}
	// view
	$this->data['heading'] = 'Edit ' . $this->data['user']->name;

	$this->data['toolbar'] = [
		\App\Libraries\View::back_link("admin/users/view/{$user_id}")
	];
	$this->data['ignore_inputs'] = [];	
	$this->data['breadcrumbs'][] = ["admin/users/view/{$user_id}", $this->data['user']->name];
	$this->data['breadcrumbs'][] = "admin/users/edit/{$user_id}";
	return view('users/edit', $this->data);
}

public function add() {
	if($this->request->getPost('save')) {
		$postUser = $this->request->getPost();
		$errors = '';
		if($postUser['password']!=$postUser['password2']) {
			$errors = ['Passwords do not match'];
		}
		if(!$errors) {
			// insert 
			unset($postUser['id']);
			$user_id = $this->usr_model->insert($postUser);
			if($user_id) {
				$this->data['messages'][] = ["Created user $user_id", 'success'];
				return $this->view($user_id);
			}
			$errors = $this->usr_model->errors();
		}
		$this->data['messages'] = $errors;
		$this->data['user'] = new \App\Entities\User($postUser);
	}
	else {
		$this->data['user'] = $this->usr_model->getNew();
	}
	
	// view
	$this->data['heading'] = "Add user";
	$this->data['ignore_inputs'] = [];
	$this->data['breadcrumbs'][] = "admin/users/add";
	$this->data['toolbar'] = [
		\App\Libraries\View::back_link("admin/users")
	];
	return view('users/edit', $this->data);
}
	
public function logins($filter='', $id='') {
	$lgn_model = new \App\Models\Logins();
	
	$delete = $this->request->getPost('del');
	if($delete) $lgn_model->delete($delete);
	$block = $this->request->getPost('block');
	if($block) {
		if($lgn_model->block_ip($block)) {
			$this->data['messages'][] = ["IP {$block} blocked", 'success'];
		}
		else {
			$this->data['messages'][] = ["Could not block IP {$block}", 'danger'];
		}
	}
	
	switch($filter) {
		case 'user_id':
			$id = intval($id);
			$this->find($id);
			$where = $filter;
			$this->data['breadcrumbs'][] = ["admin/users/view/{$id}", $this->data['user']->name];
			break;
		case 'ip':
			$where = $filter;
			break;
		default:
			$id = "";
			$where = 'error >';
	}
	
	$this->data['logins'] = [];
	foreach($lgn_model->where($where, $id)->orderBy('updated')->findAll() as $login) {
		if($filter!='user_id') {
			$user = $this->usr_model->find($login['user_id']);
		}
		$login['user_name'] = $user ? $user->name : '-' ;
		$this->data['logins'][] = $login;
	}
		
	$this->data['breadcrumbs'][] = ['admin/users/logins', 'login errors'];
	switch($filter) {
		case 'user_id':
			$this->data['heading'] = sprintf('Logins for %s', $this->data['user']->name);
			break;
		case 'ip':
			$this->data['heading'] = sprintf('Logins from IP %s', $id);
			break;
		default:
			$this->data['heading'] = 'Login errors';
	}
		
	if($filter) $this->data['breadcrumbs'][] = ["admin/users/logins/{$filter}/{$id}", "{$filter}={$id}"];
	$this->data['title'] = "logins";
	return view('users/logins', $this->data);
}

public function merge($user_id=0) {
	$this->find($user_id);
	$this->data['users'] = $this->usr_model->orderby('name')->where('id <>', $user_id)->withDeleted()->findAll();
	
	$source = intval($this->request->getPost('source'));
	if($source) {
		$source_user = $this->usr_model->withDeleted()->find($source);
		if($source_user) {
			#d($source);
			
			/* 
			need to move 
			- clubrets
			- entries
			- logins ??
			*/
			$this->data['messages'][] = ["Imported all data from {$source_user->name}", 'success'];
			$this->data['users'] = $this->usr_model->orderby('name')->where('id <>', $user_id)->withDeleted()->findAll();
		}
		else {
			$this->data['messages'][] = ["Couldn't find user {$source}", 'danger'];
		}
	}
	
	$this->data['heading'] = 'Merge user data to ' . $this->data['user']->name;
	$this->data['breadcrumbs'][] = ["admin/users/view/{$user_id}", $this->data['user']->name];
	$this->data['breadcrumbs'][] = ["admin/users/merge/{$user_id}", 'merge'];
	return view('users/merge', $this->data);

}

}
