<?php namespace App\Models;
use CodeIgniter\Model;

class Users extends Model {

protected $table = 'users';
protected $primaryKey = 'id';
protected $returnType = 'App\Entities\User';
protected $useSoftDeletes = true;
protected $updatedField  = 'updated';
protected $allowedFields = ['name', 'abbr', 'role', 'email', 'password', 'updated', 'cookie', 'reset_key', 'reset_time', 'deleted_at'];
protected $beforeUpdate = ['clean_save'];
protected $beforeInsert = ['clean_save'];

protected $validationRules = [
	'name'  => 'required|min_length[5]|is_unique[users.name,id,{id}]',
	'email' => 'valid_email',
	'password' => 'required|min_length[6]'
];

protected $validationMessages = [
	'name' => [
		'is_unique' => 'That user name has already been used.'
	],
	'email' => [
		'valid_email' => 'Please enter a valid email address'
	],
	'password' => [
		'min_length' => 'Password is not complex enough'
	]
];

protected function clean_save($arr) {
	$self = isset($arr['id']) ? in_array(session('user_id'), $arr['id']) : false;
	if($self) {
		unset($arr['data']['role']);
		unset($arr['data']['deleted_at']);
	}
	if(isset($arr['data']['password'])) {
		$arr['data']['password'] = password_hash($arr['data']['password'], PASSWORD_DEFAULT);
	}
	return $arr;
}

public function getNew() {
	$retval = new $this->returnType;
	foreach($this->allowedFields as $field) $retval->$field = '';
	return $retval;
}

public function delete_all($user_id) {
	/*
	ensure there are no entries or clubrets for this club 
	for this to happen all related events need to be deleted 
	warn if so 
	*/
	$session = session();
	$message = [];
	
	$model = new \App\Models\Clubrets;
	$records = $model->where('user_id', $user_id)->find();
	if($records) $message[] = "returns";

	$model = new \App\Models\Entries;
	$records = $model->where('user_id', $user_id)->find();
	d($records);
	if($records) $message[] = "entries";
	
	if($message) {
		$message = sprintf('There are %s for user %u', implode(' and ', $message), $user_id);
		$session->setFlashdata('messages', [$message]);
		return false;
	}
	
	$session->setFlashdata('messages', ['$this->delete($user_id, true);']);
	return true;	
}
 
} 
