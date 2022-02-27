<?php namespace App\Models;
use CodeIgniter\Model;

class Clubrets extends Model {

protected $table      = 'clubrets';
protected $primaryKey = 'id';
protected $returnType    = 'App\Entities\Clubret';
protected $allowedFields = ['event_id', 'user_id', 'name', 'address', 'phone', 'other', 'participants', 'staff'];

public function tidy() {
	$return = [];
	$sqls = [
		"SELECT `{$this->table}`.`id` FROM `{$this->table}` 
		LEFT JOIN `events` ON `{$this->table}`.`event_id` = `events`.`id` 
		WHERE `events`.`id` IS NULL AND `{$this->table}`.`deleted_at` IS NULL;",
		"SELECT `{$this->table}`.`id` FROM `{$this->table}` 
		LEFT JOIN `users` ON `{$this->table}`.`user_id` = `users`.`id` 
		WHERE `users`.`id` IS NULL AND `{$this->table}`.`deleted_at` IS NULL;"
	];
	foreach($sqls as $sql) {
		$query = $this->db->query($sql);
		foreach($query->getResult() as $row) {
			$return[] = $row->id;
			$this->delete($row->id);
		}
	}
	return count($return);
}

public function lookup($event_id, $user_id) {
	$sql = "SELECT `clubrets`.`id` FROM `clubrets` 
		INNER JOIN `events` ON `clubrets`.`event_id`=`events`.`id`
		INNER JOIN `users` ON `clubrets`.`user_id`=`users`.`id`
		WHERE `events`.`deleted_at` IS NULL 
			AND `users`.`deleted_at` IS NULL
			AND `clubrets`.`event_id`='{$event_id}'
			AND `clubrets`.`user_id`='{$user_id}'
		LIMIT 1;";
	$res = $this->query($sql)->getResultArray();
	return $res ? $this->find($res[0]['id']) : null;
}

public function lookup_all($fld_name, $fld_value) {
	$sql = "SELECT `clubrets`.`id` FROM `clubrets` 
		INNER JOIN `events` ON `clubrets`.`event_id`=`events`.`id`
		INNER JOIN `users` ON `clubrets`.`user_id`=`users`.`id`
		WHERE `events`.`deleted_at` IS NULL 
			AND `users`.`deleted_at` IS NULL
			AND `clubrets`.`{$fld_name}`='{$fld_value}';";
	$res = $this->query($sql)->getResultArray();
	return $res ? $this->find(array_column($res, 'id')) : [];
}

public function delete_event($event_id) {
	$items = $this->where('event_id', $event_id)->findAll();
	d($items);
}

} 