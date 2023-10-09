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
	'videos', 
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
	$model->where('event_id', $event_id)->delete(null, true);
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

}
