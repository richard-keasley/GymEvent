<?php namespace App\Entities;

use CodeIgniter\Entity\Entity;

class EntryCat extends Entity {
	
protected $casts = [
	'music' => 'json-array',
];

public function setSort($value) {
	$this->attributes['sort'] = sprintf('%03d', $value);
	return $this;
}

}
