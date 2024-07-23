<?php namespace App\Libraries\Mag\Rulesets;

class Fig extends Ruleset {

public function __construct() {
parent::__construct();
$this->attributes['version'] = '2021-12-11';
$this->attributes['description'] = "FIG (2022-2024)";
	
$this->_exes = [
	'FX' => [
		'name' => 'Floor',
		'method' => 'routine',
		'elm_groups' => [1, 2, 3],
		'dis_groups' => [2, 3],
		'dis_values' => null,
		'connection' => true,
		'neutrals' => [
			['deduction' => 0.3, 'description' => 'All 4 corners'],
			['deduction' => 0.3, 'description' => 'Multiple salto']
		]
	],
	'PH' => [
		'name' => 'Pommels',
		'method' => 'routine',
		'elm_groups' => [1, 2, 3],
		'dis_groups' => [4],
		'dis_values' => null,
		'connection' => false,
		'neutrals' => [
			['deduction' => 0.3, 'description' => 'Use whole horse']
		]
	],
	'SR' => [
		'name' => 'Rings',
		'method' => 'routine',
		'elm_groups' => [1, 2, 3],
		'dis_groups' => [4],
		'dis_values' => null,
		'connection' => false,
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
		'd_max' => 6
	],
	'PB' => [
		'name' => 'P-bars',
		'method' => 'routine',
		'elm_groups' => [1, 2, 3],
		'dis_groups' => [4],
		'dis_values' => null,
		'connection' => false,
		'neutrals' => []
	],
	'HB' => [
		'name' => 'High bar',
		'method' => 'routine',
		'elm_groups' => [1, 2, 3],
		'dis_values' => null,
		'dis_groups' => [4],
		'connection' => true,
		'neutrals' => []
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
	'group_max' => 5, // elements per group
];

$this->_tariff = [
	'groups' => [ 
		1 => '1',
		2 => '2',
		3 => '3',
		4 => '4',
	],
];
	
}

}
