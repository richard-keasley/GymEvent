<?php namespace App\Libraries\Rulesets;

class Ma25_u12 extends Ma25_u14 {
	
public function __construct() {
parent::__construct();
$this->attributes['description'] = "BG Under 12 (2025-2028)";

$this->_exes['SR']['neutrals'] = [];

// penalties for short routine (include zeros)
$this->_routine['short'] = [10, 6, 6, 4, 2, 0, 0, 0, 0];

}

}
