<?php namespace App\Entities;

use CodeIgniter\Entity;

class User extends Entity {
	
protected $casts = [
	'user_id' => 'integer'
];

public function self() {
	return session('user_id')===$this->id;
}

public function getAbbr() {
	return empty($this->attributes['abbr']) ? 
		substr($this->attributes['name'], 0, 4) : 
		$this->attributes['abbr'];
}

public function link($type='view', $text='') {
	switch($text) {
		case '': $text=$type; break;
	}
	switch($type) {
		case 'view': $arr = ['users', 'edit', $this->id]; break;
		case 'edit': 
		default: 
			$arr = ['users', 'view', $this->id]; break;
	}
	return sprintf('<a href="%s">%s</a>', $href = base_url($arr), $text);
}

}
