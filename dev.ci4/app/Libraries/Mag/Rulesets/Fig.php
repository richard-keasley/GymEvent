<?php namespace App\Libraries\Mag\Rulesets;

class Fig {
public $version = '2021-12-11';

public $title = "FIG";
public $description = "Senior code";

public $values = [
	'A' => .1,
	'B' => .2,
	'C' => .3,
	'D' => .4,
	'E' => .5,
	'F' => .6,
	'G' => .7,
	'H' => .8,
	'I' => .9
];

public $short = [10, 9, 8, 7, 6, 5, 4, 3, 0, 0, 0];

public $count = 10;

public $dismount = ['C' => 0.3, 'D' => 0.5 ];

public $max_group = 4;

public $neutral_deductions = [
	'FX' => [
		['deduction' => 0.3, 'description' => '4 corners'],
		['deduction' => 0.3, 'description' => 'Multiple salto'],
	],
	'PH' => [
		['deduction' => 0.3, 'description' => 'Use whole horse']
	],
	'SR' => [
		['deduction' => 0.3, 'description' => 'Swing to handstand']
	],
	'VT' => [],
	'PB' => [],
	'HB' => []
];

}
