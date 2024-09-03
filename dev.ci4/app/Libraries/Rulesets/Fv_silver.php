<?php namespace App\Libraries\Rulesets;

class Fv_silver extends Fv_gold {
	
public function __construct() {
parent::__construct();
$this->attributes['description'] = "Floor &amp; Vault (Silver)";

$this->_exes['FX']['difficulties'] = [
	'A' => 0.1,
	'B' => 0.2,
	'C' => 0.3,
	'D' => 0.4,
	'E' => 0.5,
];

$this->_exes['VT']['d_min'] = 3.5;
$this->_exes['VT']['d_max'] = 4.5;

}

}
