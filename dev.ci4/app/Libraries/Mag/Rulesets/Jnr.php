<?php namespace App\Libraries\Mag\Rulesets;

class Jnr extends Fig {

public function __construct() {
	parent::__construct();
	$this->attributes['description'] = "Junior code (Under 18s)";	
	
	$this->routine['short'] = [10, 7, 6, 5, 4, 3, 0, 0, 0];
	$this->routine['groups'][4] = ['B' => 0.3, 'C' => 0.5 ];
}

}
