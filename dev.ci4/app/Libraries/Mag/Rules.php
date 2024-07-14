<?php namespace App\Libraries\Mag;
// helper load rulesets

class Rules {
const DEF_RULESETNAME = 'Fig';

// ruleset basename(file) => title
const index = [
	self::DEF_RULESETNAME => 'FIG',
	'Jnr' => 'FIG - Junior',
	'U14' => 'BG - Under 14',
	'U12' => 'BG - Under 12'
];

static function load($setname=self::DEF_RULESETNAME) {
	if(!self::exists($setname)) $setname = self::DEF_RULESETNAME;
	$classname = "\\App\\Libraries\\Mag\\Rulesets\\{$setname}";
	return new $classname;
	/*
	$ruleset = new $classname;
	$ruleset->name = $classname;
	$ruleset->title = self::index[$setname];
	return $ruleset;
	*/
}

static function exists($setname) {
	return isset(self::index[$setname]);
}
	
}
