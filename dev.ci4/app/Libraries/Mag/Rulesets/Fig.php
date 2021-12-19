<?php namespace App\Libraries\Mag\Rulesets;

class Fig {
public $name = null;
public $title = null;

public $version = '2021-12-11';

public $description = 'FIG senior code';

public $exes = [
	'FX' => [
		'name' => 'Floor',
		'method' => 'routine',
		'dis_groups' => [2, 3],
		'connection' => true,
		'neutrals' => [
			['deduction' => 0.3, 'description' => 'All 4 corners'],
			['deduction' => 0.3, 'description' => 'Multiple salto']
		]
	],
	'PH' => [
		'name' => 'Pommels',
		'method' => 'routine',
		'dis_groups' => [4],
		'connection' => false,
		'neutrals' => [
			['deduction' => 0.3, 'description' => 'Use whole horse']
		]
	],
	'SR' => [
		'name' => 'Rings',
		'method' => 'routine',
		'dis_groups' => [4],
		'connection' => false,
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
		'dis_groups' => [4],
		'connection' => false,
		'neutrals' => []
	],
	'HB' => [
		'name' => 'High bar',
		'method' => 'routine',
		'dis_groups' => [4],
		'connection' => true,
		'neutrals' => []
	]
];

public $routine = [
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
	'group_max' => 4, // elements per group
	'group_dis' => 4 // dismount group
];

public $tariff = [
	'count' => 1 // number of exercises
];

public function routine_options($propname) {
	$options = [''];
	foreach($this->routine[$propname] as $key=>$val) {
		$options[$key] = $key;
	}
	return $options;
}

}
