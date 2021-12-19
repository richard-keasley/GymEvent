<?php namespace App\Libraries\Mag\Rulesets;

class Bg_under12 extends Fig {
public $title = "BG under 12";
public $description = "BG Junior code (Under 12s)";	

public function __construct() {
	$this->routine['short'] = [10, 7, 6, 5, 4, 0, 0, 0, 0];
	$this->routine['groups'][4] = ['A' => 0.3, 'B' => 0.5 ];
	$this->exes['FX']['neutrals'] = [
		['deduction' => 0.3, 'description' => 'All 4 corners']
	];
	$this->exes['SR']['neutrals'] = [];
}

}
