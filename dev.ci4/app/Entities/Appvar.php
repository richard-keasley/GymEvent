<?php namespace App\Entities;

use CodeIgniter\Entity;

class Appvar extends Entity {

protected $casts = [
	'value' => 'json-array'
];


}
