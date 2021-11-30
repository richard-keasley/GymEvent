<?php namespace App\Entities;

use CodeIgniter\Entity;

class EntryCat extends Entity {
	
protected $casts = [
	'music' => 'csv',
	'videos' => 'csv'
];

}
