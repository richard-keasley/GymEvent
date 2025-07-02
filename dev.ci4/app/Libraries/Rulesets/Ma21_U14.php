<?php namespace App\Libraries\Rulesets;

class Ma21_U14 extends Ma21_Fig {

public function __construct() {
	parent::__construct();
	$this->attributes['description'] = "BG Junior code (Under 14s)";	
	$this->attributes['version'] = '2022-01-14';
	
	$this->_routine['short'] = [10, 7, 6, 5, 4, 3, 0, 0, 0];
	$this->_routine['groups'][4] = ['B' => 0.3, 'C' => 0.5 ];
	$this->_exes['FX']['neutrals'] = [
		['deduction' => 0.3, 'description' => 'All 4 corners']
	];
	$this->_exes['FX']['dis_values'] = $this->_routine['groups'][4];
}

}
