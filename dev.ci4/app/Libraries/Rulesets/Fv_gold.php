<?php namespace App\Libraries\Rulesets;

class Fv_gold extends Ruleset {
	
public function __construct() {
parent::__construct();
$this->attributes['version'] = '2024-08-08';
$this->attributes['description'] = "Floor &amp; Vault (Gold)";

$this->_exes = [
	'FX' => [
		'name' => 'Floor',
		'method' => 'routine',
		'group_labels' => [
			1 => 'Jumps, leaps &amp turns',
			2 => 'Strength &amp; balance',
			3 => 'Rotation',
		],
	],
	'VT' => [
		'name' => 'Vault',
		'method' => 'tariff',
		'group_labels' => [
			1 => 'jump',
			2 => 'mat pile',
			3 => 'vault',
		],
		# 'd_min' => 0,
		# 'd_max' => 0,
	],
];

$skills = $this->skills('VT');
$tariffs = array_column($skills['skills'], 'tariff');
$this->_exes['VT']['d_min'] = (float) min($tariffs);
$this->_exes['VT']['d_max'] = (float) max($tariffs);

$this->_routine = [
	'difficulties' => [
		'A' => 0.1,
		'B' => 0.2,
		'C' => 0.3,
		'D' => 0.4,
		'E' => 0.5,
		'F' => 0.6,
	],
	// penalties for short routine (include zeros)
	'short' => [10, 9, 8, 7, 6, 5, 4, 3, 2, 1, 0],
	// value for each group according to difficulty
	'groups' => [ 
		1 => ['A' => 0.5],
		2 => ['A' => 0.5],
		3 => ['A' => 0.5],
	],
	'group_max' => 6, // elements per group
	'elm_groups' => [1, 2, 3],
	'dismount' => false, // last element==dismount
	'dis_groups' => [1, 2, 3], // valid last element
	'dis_values' => [],
	'neutrals' => [],
	'connection' => false,
	'selector' => true, // skill selector available
];

$this->_tariff = [
	'groups' => [ 
		1 => 0,
		2 => 0,
		3 => 0,
		4 => 0,
	],
	'neutrals' => [],
	'connection' => false,
	'exe_count' => 1,
	'selector' => true
];

}

function skills($exekey) {
	$skills = parent::skills($exekey);

	$retval = [];
	$ret_skill = [];
	switch($exekey) {
		case 'VT':
		$keys = ['id', 'tariff', 'group', 'description'];
		$filter = $this->title[0];
		foreach($skills['skills'] as $skill) {
			$include = $skill[$filter] ?? false;
			if(!$include) continue;
			foreach($keys as $key) {
				$ret_skill[$key] = $skill[$key];
			}
			$retval[] = $ret_skill;
		}
		$skills['skills'] = $retval;
		break;
		
		case 'FX':
		$diffs = $this->$exekey['difficulties'] ?? [];
		foreach($skills['skills'] as $skill) {
			$include = isset($diffs[$skill['difficulty']]);
			if(!$include) continue;
			$retval[] = $skill;
		}
		$skills['skills'] = $retval;
		break;
	}
	return $skills;
}

}
