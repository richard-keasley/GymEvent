<?php namespace App\Libraries\Mag\Rulesets;

class U12 extends Fig {
public $description = "BG Junior code (Under 12s)";	
public $version = '2022-01-14';

public function __construct() {
	$this->routine['short'] = [10, 6, 6, 4, 2, 0, 0, 0, 0];
	$this->routine['groups'][4] = ['A' => 0.3, 'B' => 0.5 ];
	$this->exes['FX']['neutrals'] = [
		['deduction' => 0.3, 'description' => 'All 4 corners']
	];
	$this->exes['SR']['neutrals'] = [];
}

}
