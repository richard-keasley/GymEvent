<?php namespace App\Libraries\Rulesets;

class Ma21_Jnr extends Ma21_Fig {

public function __construct() {
	parent::__construct();
	$this->attributes['description'] = "Junior code (Under 18s)";	
	
	$this->_routine['short'] = [10, 7, 6, 5, 4, 3, 0, 0, 0];
	$this->_routine['groups'][4] = ['B' => 0.3, 'C' => 0.5 ];
	$this->_exes['FX']['dis_values'] = $this->_routine['groups'][4];
}

}
