<?php namespace App\Libraries\Rulesets;

class Ma21_Fig extends Ruleset {

public function __construct() {
parent::__construct();
$this->attributes['version'] = '2021-12-11';
$this->attributes['description'] = "FIG (2022-2024)";
	
$this->_exes = [
	'FX' => [
		'name' => 'Floor',
		'method' => 'routine',
		'groups' => [ 
			1 => ['A' => 0.5],
			2 => ['A' => 0.5],
			3 => ['A' => 0.5],
		],
		'elm_groups' => [1, 2, 3],
		'dis_groups' => [2, 3],
		'dis_values' => [
			'C' => 0.3,
			'D' => 0.5,
		],
		'connection' => true,
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
		'neutrals' => [],
		'exe_count' => 2,
		'd_min' => 0.1,
		'd_max' => 6,
	],
	'PB' => [
		'name' => 'P-bars',
		'method' => 'routine',
	],
	'HB' => [
		'name' => 'High bar',
		'method' => 'routine',
		'connection' => true,
	]
];

$this->_routine = [
	'difficulties' => [
		'A' => 0.1,
		'B' => 0.2,
		'C' => 0.3,
		'D' => 0.4,
		'E' => 0.5,
		'F' => 0.6,
		'G' => 0.7,
		'H' => 0.8,
		'I' => 0.9
	],
	// penalties for short routine (include zeros)
	'short' => [10, 9, 8, 7, 6, 5, 4, 3, 0, 0, 0],
	// value for each group according to difficulty
	'groups' => [ 
		1 => ['A' => 0.5],
		2 => ['A' => 0.5],
		3 => ['A' => 0.5],
		4 => ['C' => 0.3, 'D' => 0.5 ]
	],
	'group_labels' => [],
	'group_max' => 5, // elements per group
	'elm_groups' => [1, 2, 3],
	'dismount' => true, // last element==dismount
	'dis_groups' => [4],
	'dis_values' => [],
	'connection' => false,
	'neutrals' => [],
];

$this->_tariff = [
	'groups' => [ 
		1 => [],
		2 => [],
		3 => [],
		4 => [],
	],
	'connection' => false,
];
	
}

}
