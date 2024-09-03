<?php namespace App\Libraries\Rulesets;

class Fv_intermediate extends Fv_gold {
	
public function __construct() {
parent::__construct();
$this->attributes['description'] = "Floor &amp; Vault (intermediate)";

$this->_exes['FX']['difficulties'] = [
	'A' => 0.1,
	'B' => 0.2,
];

$this->_exes['VT']['d_min'] = 2.5;
$this->_exes['VT']['d_max'] = 3.0;

}

}
