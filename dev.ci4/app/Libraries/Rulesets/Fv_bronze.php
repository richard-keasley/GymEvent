<?php namespace App\Libraries\Rulesets;

class Fv_bronze extends Fv_gold {
	
public function __construct() {
parent::__construct();
$this->attributes['description'] = "Floor &amp; Vault (Silver)";

$this->_exes['FX']['difficulties'] = [
	'A' => 0.1,
	'B' => 0.2,
	'C' => 0.3,
	'D' => 0.4,
];

}

}
