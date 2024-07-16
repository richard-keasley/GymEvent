<?php namespace App\Libraries\Mag\Rulesets;

class Fig_2025 {
	
protected $attributes = [
	'name' => null,
	'title' => null,
	'version' => '2024-07-15',
	'description' => "FIG code (2025-2028)"
];
public function __get($key) {
	return $this->attributes[$key] ?? null;
}
public function __construct() {
	$classname = get_class($this);
	$arr = explode('\\', $classname);
	$name = array_pop($arr);
	$this->attributes['name'] = $name;
	$this->attributes['title'] = \App\Libraries\Mag\Rules::index[$name];
}

public $exes = [
	'FX' => [
		'name' => 'Floor',
		'method' => 'routine',
		'elm_groups' => [1, 2, 3, 4],
		'dis_groups' => [2, 3, 4],
		'dis_values' => null,
		'connection' => true,
		'neutrals' => [
			['deduction' => 0.3, 'description' => 'All 4 corners'],
			['deduction' => 0.3, 'description' => 'Multiple salto'],
			['deduction' => 0.3, 'description' => 'Single leg balance']
		]
	],
	'PH' => [
		'name' => 'Pommels',
		'method' => 'routine',
		'elm_groups' => [1, 2, 3],
		'dis_groups' => [4],
		'dis_values' => [
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
		'dis_values' => [
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
		'd_max' => 6
	],
	'PB' => [
		'name' => 'P-bars',
		'method' => 'routine',
		'elm_groups' => [1, 2, 3],
		'dis_groups' => [4],
		'dis_values' => [
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
		'connection' => false,
		'neutrals' => []
	],
	'HB' => [
		'name' => 'High bar',
		'method' => 'routine',
		'elm_groups' => [1, 2, 3],
		'dis_groups' => [4],
		'dis_values' => [
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
	'short' => [10, 7, 6, 5, 4, 3, 0, 0, 0],
	// value for each group according to difficulty
	'groups' => [ 
		1 => ['A' => 0.5],
		2 => ['A' => 0.3, 'D' => 0.5 ],
		3 => ['A' => 0.3, 'D' => 0.5 ],
		4 => ['A' => 0.3, 'D' => 0.5 ],
	],
	'group_max' => 4, // elements per group
];

public function routine_options($propname) {
	$options = [''];
	foreach($this->routine[$propname] as $key=>$val) {
		$options[$key] = $key;
	}
	return $options;
}

}
