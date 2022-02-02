<?php namespace App\Libraries\Mag\Rulesets;

class U14 extends Fig {
public $description = "BG Junior code (Under 14s)";	
public $version = '2022-01-14';

public function __construct() {
	$this->routine['short'] = [10, 7, 6, 5, 4, 3, 0, 0, 0];
	$this->routine['groups'][4] = ['B' => 0.3, 'C' => 0.5 ];
	$this->exes['FX']['neutrals'] = [
		['deduction' => 0.3, 'description' => 'All 4 corners']
	];
}

}
