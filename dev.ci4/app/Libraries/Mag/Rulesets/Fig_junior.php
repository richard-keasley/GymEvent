<?php namespace App\Libraries\Mag\Rulesets;

class Fig_junior extends Fig {
public $title = "Junior FIG";
public $description = "Junior code (Under 18s)";	

public function __construct() {
	$this->routine['short'] = [10, 7, 6, 5, 4, 3, 0, 0, 0];
	$this->routine['count'] = 8;
	$this->routine['dismount'] = ['B' => 0.3, 'C' => 0.5 ];
}

}
