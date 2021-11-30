<?php namespace App\Models;
use CodeIgniter\Model;

class Videos extends Model {
	
protected $table      = 'evt_entries';
protected $primaryKey = 'id';
protected $allowedFields = ['videos'];
protected $returnType   = 'App\Entities\Entry';
		
}