<?php namespace App\Models;

use CodeIgniter\Model;

class Events extends Model {
	
protected $table      = 'events';
protected $primaryKey = 'id';
protected $returnType = 'App\Entities\Event';
protected $useSoftDeletes = true;
protected $deletedField  = 'deleted_at';
protected $allowedFields = [
	'title', 
	'date', 
	'dates', 
	'description', 
	'payment', 
	'participants', 
	'staff', 
	'clubrets', 
	'music', 
	'player', 
	'discats', 
	'staffcats', 
	'stafffee', 
	'deleted_at',
	'private'
];
protected $validationRules = [
	'title' => 'required|alpha_numeric_punct|min_length[5]',
	'date' =>'required'
];

public function delete_all($event_id) {
	$event = $this->onlyDeleted()->find($event_id);
	if(!$event) return false;
	
	$this->delete($event_id, true);
	
	// remove orphans
	$sql = "SELECT `evt_disciplines`.`id` FROM `evt_disciplines` 
	LEFT JOIN `events` ON `evt_disciplines`.`event_id` = `events`.`id`
	WHERE `events`.id IS NULL;";
	$query = $this->db->query($sql);
	foreach($query->getResult() as $row) {
		$sql = "DELETE FROM `evt_disciplines` WHERE `evt_disciplines`.`id`={$row->id};";
		$this->db->query($sql);
	}

	$sql = "SELECT `evt_categories`.`id` FROM `evt_categories` 
	LEFT JOIN `evt_disciplines` ON `evt_categories`.`discipline_id` = `evt_disciplines`.`id`
	WHERE `evt_disciplines`.id IS NULL;";
	$query = $this->db->query($sql);
	foreach($query->getResult() as $row) {
		$sql = "DELETE FROM `evt_categories` WHERE `evt_categories`.`id`={$row->id};";
		$this->db->query($sql);
	}

	$sql = "SELECT `evt_entries`.`id` FROM `evt_entries` 
	LEFT JOIN `evt_categories` ON `evt_entries`.`category_id` = `evt_categories`.`id`
	WHERE `evt_categories`.id IS NULL;";
	$query = $this->db->query($sql);
	foreach($query->getResult() as $row) {
		$sql = "DELETE FROM `evt_entries` WHERE `evt_entries`.`id`={$row->id};";
		$this->db->query($sql);
	}

	$sql = "SELECT `clubrets`.`id` FROM `clubrets` 
	LEFT JOIN `events` ON `clubrets`.`event_id` = `events`.`id`
	WHERE `events`.id IS NULL;";
	$query = $this->db->query($sql);
	foreach($query->getResult() as $row) {
		$sql = "DELETE FROM `clubrets` WHERE `clubrets`.`id`={$row->id};";
		$this->db->query($sql);
	}
	return true;	
}

static function files() {
	$filepath = FCPATH . "public/events";
	$files = new \CodeIgniter\Files\FileCollection();
	if(is_dir($filepath)) {
		$files->addDirectory($filepath, true);
	}
	return $files;
}

}
