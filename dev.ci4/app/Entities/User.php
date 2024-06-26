<?php namespace App\Entities;

use CodeIgniter\Entity\Entity;

class User extends Entity {
	
protected $casts = [
	'enabled' => 'integer'
];

public function self() {
	return session('user_id')===$this->id;
}

public function getAbbr() {
	$val = $this->attributes['abbr'] ? $this->attributes['abbr'] : $this->attributes['name'];
	return substr($val, 0, 5);
}

public function link() {
	$path = "admin/users/view/{$this->id}";
	if(!\App\Libraries\Auth::check_path($path)) return '';
	$label = sprintf('<i class="bi bi-person text-primary" title="View user %s"></i>', $this->name);
	return anchor($path, $label);
}

public function clubrets() {
	$retval = [];
	$model = new \App\Models\Clubrets;
	// only returns if event is listed
	$sql = "SELECT `clubrets`.`id` FROM `clubrets` 
		INNER JOIN `events` ON `clubrets`.`event_id`=`events`.`id`
		WHERE `events`.`deleted_at` IS NULL 
		AND `clubrets`.`user_id`='{$this->id}';";
	$res = $model->query($sql)->getResultArray();
	return $res ? $model->find(array_column($res, 'id')) : [] ;
}

public function placeholders() {
	$retval = [];
	foreach($this->toArray() as $key=>$val) {
		switch($key) {
			// ignore these 
			case 'password':
			case 'cookie':
			break;
			
			default:
			$retval[$key] = $val;
		}
	}
	return $retval;	
}

}
