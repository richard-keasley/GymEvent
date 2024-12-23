<?php namespace App\Libraries\Rulesets;

class Ma25_u14 extends Ma25 {
	
public function __construct() {
parent::__construct();
$this->attributes['version'] = '2024-11-12';
$this->attributes['description'] = "BG Under 14 (2025-2028)";
	
$this->_exes['FX']['neutrals'] = [
	['deduction' => 0.3, 'description' => 'All 4 corners'],
	['deduction' => 0.3, 'description' => 'Single leg balance']
];

}

}
