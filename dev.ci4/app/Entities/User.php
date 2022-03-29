<?php namespace App\Entities;

use CodeIgniter\Entity;

class User extends Entity {
	
protected $casts = [
	'user_id' => 'integer'
];

public function self() {
	return session('user_id')===$this->id;
}

public function getAbbr() {
	return empty($this->attributes['abbr']) ? 
		substr($this->attributes['name'], 0, 4) : 
		$this->attributes['abbr'];
}

public function link() {
	$label = sprintf('<i class="bi bi-person text-primary" title="View user %s"></i>', $this->name);
	return getlink("admin/users/view/{$this->id}", $label);
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

}
