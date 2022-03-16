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
	$model = new \App\Models\Clubrets;
	$model->where('user_id', $user_id)->delete(null, true);
	$model = new \App\Models\Entries;
	$model->where('user_id', $user_id)->delete(null, true);
	$this->delete($user_id, true);
	return true;	
}

public function entries($event_id) {
	// all users entered into event
	$event_id = intval($event_id);
	$sql = "SELECT DISTINCT `users`.* 
	FROM `users` 
	INNER JOIN `evt_entries` ON `users`.`id`=`evt_entries`.`user_id` 
	INNER JOIN `evt_categories` ON `evt_entries`.`category_id`= `evt_categories`.`id` 
	INNER JOIN `evt_disciplines` ON `evt_categories`.`discipline_id`=`evt_disciplines`.`id` 
	INNER JOIN `events` ON `evt_disciplines`.`event_id`=`events`.`id` 
	WHERE `events`.`id`={$event_id}
	ORDER BY `users`.`name`";
	$query = $this->query($sql);

	$retval = [];
	foreach($query->getResultArray() as $row) {
		$retval[] = new \App\Entities\User($row);
	}
	return $retval;
}
 
} 
