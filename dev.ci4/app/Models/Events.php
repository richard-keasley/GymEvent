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
	'description', 
	'payment', 
	'clubrets', 
	'music', 
	'player', 
	'videos', 
	'discats', 
	'staffcats', 
	'deleted_at'
];
protected $validationRules = [
	'title' => 'required|alpha_numeric_punct|min_length[5]',
	'date' =>'required'
];

public function delete_all($event_id) {
	$model = new \App\Models\Clubrets;
	$model->where('event_id', $event_id)->delete(null, true);
	$model = new \App\Models\Entries;
	$model->delete_event($event_id);
	$this->delete($event_id, true);
	
	$session = session();
	$session->setFlashdata('messages', ["Deleted event {$event_id}"]);
	return true;	
}

}
