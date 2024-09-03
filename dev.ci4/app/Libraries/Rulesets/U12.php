<?php namespace App\Libraries\Rulesets;

class U12 extends Fig {

public function __construct() {
	parent::__construct();
	$this->attributes['description'] = "BG Junior code (Under 12s)";	
	$this->attributes['version'] = '2022-01-14';
	
	$this->_routine['short'] = [10, 6, 6, 4, 2, 0, 0, 0, 0];
	$this->_routine['groups'][4] = ['A' => 0.3, 'B' => 0.5 ];
	
	$this->_exes['FX']['neutrals'] = [
		['deduction' => 0.3, 'description' => 'All 4 corners']
	];
	$this->_exes['FX']['dis_values'] = $this->_routine['groups'][4];
		
	$this->_exes['SR']['neutrals'] = [];
}

}
