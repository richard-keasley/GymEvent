<?php namespace App\Libraries\Mag\Rulesets;

class Fig {
public $version = '2021-12-11';

public $title = "FIG";
public $description = "Senior code";

public $exes = [
	'FX' => [
		'name' => 'Floor',
		'method' => 'routine',
		'neutrals' => [
			['deduction' => 0.3, 'description' => 'All 4 corners'],
			['deduction' => 0.3, 'description' => 'Multiple salto']
		]
	],
	'PH' => [
		'name' => 'Pommels',
		'method' => 'routine',
		'neutrals' => [
			['deduction' => 0.3, 'description' => 'Use whole horse']
		]
	],
	'SR' => [
		'name' => 'Rings',
		'method' => 'routine',
		'neutrals' => [
			['deduction' => 0.3, 'description' => 'Swing to handstand']
		]
	],
	'VT' => [
		'name' => 'Vault',
		'method' => 'tariff',
		'neutrals' => []
	],
	'PB' => [
		'name' => 'P-bars',
		'method' => 'routine',
		'neutrals' => []
	],
	'HB' => [
		'name' => 'High bar',
		'method' => 'routine',
		'neutrals' => []
	]
];

public $routine = [
	'values' => [
		'A' => .1,
		'B' => .2,
		'C' => .3,
		'D' => .4,
		'E' => .5,
		'F' => .6,
		'G' => .7,
		'H' => .8,
		'I' => .9
	],
	'short' => [10, 9, 8, 7, 6, 5, 4, 3, 0, 0, 0],
	'count' => 10,
	'group_vals' => [
		1 => ['A' => 0.3, 'B' => 0.5],
		2 => ['A' => 0.5],
		3 => ['A' => 0.5],
		4 => ['C' => 0.3, 'D' => 0.5 ]
	],
	'max_group' => 4
];

public $tariff = [
	'count' => 1
];

}
