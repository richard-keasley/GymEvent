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
	$session = \Config\Services::session();
	$session->setFlashdata('messages', ["ToDo: cascade delete event"]);
}

}
