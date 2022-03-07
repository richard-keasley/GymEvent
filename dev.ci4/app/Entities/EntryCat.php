<?php namespace App\Entities;

use CodeIgniter\Entity;

class EntryCat extends Entity {
	
protected $casts = [
	'music' => 'json-array',
	'videos' => 'json-array'
];

}
