<?php namespace App\Libraries\Rulesets;

class Fv_novice extends Fv_gold {
	
public function __construct() {
parent::__construct();
$this->attributes['description'] = "Floor &amp; Vault (novice)";

$this->_exes['FX']['difficulties'] = [
	'A' => 0.1,
];
$this->_exes['FX']['group_max'] = 5;

}

}
