<?php namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Html extends Entity implements \stringable {

public function __tostring() {
	$retval = $this->value ?? '';
	try {
		$dt = new \datetime($this->updated);
		$format = '<p class="bg-light p-1 border d-inline-block">Updated: %s</p>';
		$retval .= sprintf($format, $dt->format('j M Y'));
	}
	catch(\throwable $ex) {}
	
	return $retval;
}

}
