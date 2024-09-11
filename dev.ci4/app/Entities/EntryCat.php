<?php namespace App\Entities;

use CodeIgniter\Entity\Entity;

class EntryCat extends Entity {
	
protected $casts = [
	'music' => 'json-array',
];

}
