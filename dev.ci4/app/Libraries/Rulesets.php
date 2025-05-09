<?php namespace App\Libraries;

class Rulesets {

// ruleset basename(file) => title
const index = [
	'mag' => [
		/* 2025 CoP */
		'Ma25' => 'FIG',
		'Ma25_u14' => 'Under 14',
		'Ma25_u12' => 'Under 12',
		
		/* 2021 CoP 
		'Ma21_Fig' => '(old) FIG ',
		'Ma21_Jnr' => '(old) Junior',
		'Ma21_U14' => '(old) Under 14',
		'Ma21_U12' => '(old) Under 12',
		*/
	],
	'general' => [
		/* 2025 Reg FV */
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
