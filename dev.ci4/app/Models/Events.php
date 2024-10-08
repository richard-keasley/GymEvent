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
	
	self::delete_path(dirname($event->filepath()));
	
	$model = new \App\Models\Clubrets;
	$model->delete_event($event_id);
	
	$model = new \App\Models\Entries;
	$model->delete_event($event_id);
	
	$this->delete($event_id, true);
	return true;
}

static function delete_path($path) {
	if(is_dir($path)) { 
		foreach(scandir($path) as $object) { 
			if($object != "." && $object != "..") {
				$object = $path. DIRECTORY_SEPARATOR .$object;
				if(is_dir($object) && !is_link($object)) {
					self::delete_path($object);
				}
				if(is_file($object)) {
					unlink($object); 
				}
			} 
		}
		rmdir($path); 
	} 
}

static function disk_space() {
	$filepath = FCPATH . "public/events";
	$files = new \CodeIgniter\Files\FileCollection();
	if(is_dir($filepath)) {
		$files->addDirectory($filepath, true);
	}
	$file_size = 0;
	foreach($files as $file) $file_size += $file->getSize();
	return [
		'count' => count($files),
		'size' => $file_size
	];
}

}
