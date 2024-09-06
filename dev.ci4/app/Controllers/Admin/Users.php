<?php namespace App\Controllers\Admin;

class Users extends \App\Controllers\BaseController {

public function __construct() {
	$this->usr_model = new \App\Models\Users();
	$this->data['breadcrumbs'][] = 'admin';
	$this->data['breadcrumbs'][] = 'admin/users';
	$this->data['base_url'] = site_url(['admin', 'users']);
	$this->data['user_self'] = false;
}

private function find($user_id) {
	$user_id = intval($user_id);
	$this->data['user'] = $this->usr_model->withDeleted()->find($user_id);
	if(!$this->data['user']) {
		$message = "Can't find user {$user_id}";
		throw \App\Exceptions\Exception::not_found($message);
	}
	
	
	$this->data['user_self'] = $this->data['user']->self();
	$this->data['title'] = $this->data['user']->name;
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
	$cmd = $this->request->getPost('cmd');
	$item_id = intval($this->request->getPost('item_id'));
	if($cmd=='del_user' && $item_id) {
		if($this->usr_model->delete($item_id, true)) {
			$this->data['messages'][] = ["User {$item_id} deleted", 'success'];
		}
		else {
			$this->data['messages'] = $this->usr_model->errors();
			$this->data['messages'][] = "User {$item_id} not deleted.";
		}
	}
		
	$update = $this->request->getPost('update');
	if($update) {
		$roles = [''];
		foreach(\App\Libraries\Auth::roles as $role) {
			$roles[] = $role;
			if($role=='club') break;
		}
	}
		
	// read
	$filter_by = $this->request->getGet('by');
	$filter_val = $this->request->getGet('val');
	
	$sorts = ['name', 'updated', 'role'];
	$sort = $this->request->getGet('sort');
	if(!in_array($sort, $sorts)) $sort = current($sorts);
	$this->usr_model->orderBy($sort);
	
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
	
	// view 
	$min_role = \App\Libraries\Auth::$min_role;
	if($min_role!=\App\Libraries\Auth::roles[0]) {
		$this->data['messages'][] = ["Minimum login role is '{$min_role}'", 'warning'];
	}
	
	$this->data['modal_delete'] = [
		'cmd' => 'del_user',
		'title' => 'Delete <span class="dataname">user</span>',
		'description' => '<p>Delete this user? (Event entries are preserved.)</p>',
		'item_id' => 0
	];
	return view('users/index', $this->data);
}

public function view($user_id=0) {
	$this->find($user_id);
	
	if(!$this->data['user_self']) {
				
		$set_enabled = $this->request->getPost('enable');
		if($set_enabled) {
			switch($set_enabled) {
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
				$session = \Config\Services::session();
				$session->setFlashdata('messages', $this->data['messages']);
				return redirect()->to(site_url());
			}
			else $this->data['messages'][] = "Couldn't login as $user_id";
		}
	}
	
	if($this->request->getPost('cmd')=='modalUser') {
		$source_user = $this->usr_model->find($this->request->getPost('user_id'));
		if($source_user) {
			$source_id = $source_user->id;
			$model = new \App\Models\Clubrets;
			$model->where('user_id', $source_id)->set(['user_id'=>$user_id])->update();
			$model = new \App\Models\Entries;
			$model->where('user_id', $source_id)->set(['user_id'=>$user_id])->update();
			$this->usr_model->delete($source_id, true);
			$this->data['messages'][] = "Merged data from {$source_user->name} ({$source_id}) and deleted user.";
		}
		else {
			$this->data['messages'][] = "Can't find user {$source_id}";
		}
	}
	
	$exclude = [$user_id, session('user_id')];
	$this->data['users_dialogue'] = [
		'title' => 'Merge data from another user',
		'user_id' => $this->data['user']->user_id,
		'users' => $this->usr_model->orderby('name')->whereNotIn('id', $exclude)->findAll(),
		'description' => sprintf('Select user to merge from. User data will be pulled into <em>%s</em>. <span class="bg-danger-subtle">The selected user will be deleted</span>.', $this->data['user']->name)
	];
		
	// view
	$this->data['heading'] = $this->data['user']->name;
	$this->data['breadcrumbs'][] = ["admin/users/view/{$user_id}", $this->data['user']->name];
	
	$this->data['toolbar'] = [
		\App\Libraries\View::back_link('admin/users')
	];
	
	if(!$this->data['user']->deleted_at) {
		$this->data['toolbar'][] = getlink("admin/users/edit/{$user_id}", 'edit');
		$this->data['toolbar'][] = sprintf('<a href="%s/%u" class="btn btn-outline-secondary">logins</a>', site_url('admin/users/logins/user_id'), $user_id);
		$this->data['toolbar'][] = '<button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modalUser" title="Merge data from another user"><span class="bi bi-layer-backward"></span></button>';
	}
	
	if(!$this->data['user_self']) {
		if($this->data['user']->deleted_at) {
			$this->data['toolbar'][] = '<button name="enable" value="enable" type="submit" title="enable" class="btn btn-success bi-check-circle"></button>';
			$this->data['toolbar'][] = '<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#del_user" title="Delete this user"><span class="bi bi-trash"></span></button>';
			$this->data['modal_delete'] = [
				'action' => 'admin/users',
				'id' => 'del_user',
				'title' => "Delete '{$this->data['user']->name}'",
				'description' => '<p>Delete this user? (Event entries are preserved.)</p>',
				'cmd' => "del_user",
				'item_id' => $this->data['user']->id
			];
		}
		else {
			$this->data['toolbar'][] = '<button name="enable" value="disable" type="submit" title="disable" class="btn bi-x-circle btn-danger"></button>';
			if(\App\Libraries\Auth::check_path('superuser')) { 
				$this->data['toolbar'][] = '<button name="loginas" value="1" type="submit" class="btn btn-secondary">login as&hellip;</button>';
			}
		}
	}
	return view('users/view', $this->data);
}

public function edit($user_id=0) {
	// compare to /user/edit
	$this->find($user_id);

	if(!\App\Libraries\Auth::check_role($this->data['user']->role)) {
		$message = "You can not edit this user";
		throw \App\Exceptions\Exception::forbidden($message);
	}
		
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
			# $where = 'error >';
			$where = 'id >';
	}
	
	$user_names = [];
	$this->data['logins'] = [];
	$ipinfo = new \App\Libraries\Ipinfo;
	$ip_keys = ['city', 'countryCode'];
	foreach($lgn_model->where($where, $id)->orderBy('updated')->findAll() as $login) {
		if($filter=='user_id') {
			$login['user_name'] = $this->data['user']->name;
		}
		else {
			$uid = $login['user_id'];
			if(!isset($user_names[$uid])) {
				if($uid) {
					$tmp_user = $this->usr_model->find($uid);
					$user_names[$uid] = $tmp_user ? $tmp_user->name : '???' ;
				}
				else {
					$user_names[$uid] = 'none';
				}
			}
			$login['user_name'] = $user_names[$uid];
		}
		$login['check_ip'] = $lgn_model->check_ip($login['ip']);		
		$login['ip_info'] = $ipinfo->get($login['ip'])->attributes($ip_keys);

		$this->data['logins'][] = $login;
	}
		
	$this->data['breadcrumbs'][] = ['admin/users/logins', 'logins'];
	switch($filter) {
		case 'user_id':
			$this->data['heading'] = sprintf('Logins for %s', $this->data['user']->name);
			break;
		case 'ip':
			$this->data['heading'] = sprintf('Logins from IP %s', $id);
			break;
		default:
			$this->data['heading'] = 'Logins';
	}
		
	if($filter) $this->data['breadcrumbs'][] = ["admin/users/logins/{$filter}/{$id}", "{$filter}={$id}"];
	$this->data['title'] = "logins";
	return view('users/logins', $this->data);
}

}
