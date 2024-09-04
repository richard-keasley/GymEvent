<?php namespace App\Libraries;

class Rulesets {

// ruleset basename(file) => title
const index = [
	'mag' => [
		'Fig' => 'FIG (2022)',
		'Jnr' => 'FIG - Junior',
		'U14' => 'BG - Under 14',
		'U12' => 'BG - Under 12',
		'Fig_2025' => 'FIG (2025)'
	],
	'general' => [
		'Fv_gold' => 'Gold',
		'Fv_silver' => 'Silver',
		'Fv_bronze' => 'Bronze',
		'Fv_advanced' => 'Advanced',
		'Fv_intermediate' => 'Intermediate',
		'Fv_novice' => 'Novice',
	]
];

static function load($setname) {
	if(!self::exists($setname)) {
		$dis = array_key_first(self::index);
		$setname = array_key_first(self::index[$dis]);
	}
	return new ("\\App\\Libraries\\Rulesets\\{$setname}");
}

static function title($key) {
	$options = self::options();
	return $options[$key] ?? '?not found' ;
}

static function exists($setname) {
	$options = self::options();
	return isset($options[$setname]);
}

static function options($discipline='all') {
	if($discipline=='all') {
		$retval = [];
		foreach(self::index as $arr) {
			$retval = array_merge($retval, $arr);
		}
		return $retval;
	}
	
	return self::index[$discipline] ?? [];
}

}