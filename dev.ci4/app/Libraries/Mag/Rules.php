<?php namespace App\Libraries\Mag;
// helper load rulesets

class Rules {
const DEF_RULESETNAME = 'Fig';

// ruleset basename(file) => title
const index = [
	self::DEF_RULESETNAME => 'FIG (2022)',
	'Jnr' => 'FIG - Junior',
	'U14' => 'BG - Under 14',
	'U12' => 'BG - Under 12',
	'Fig_2025' => 'FIG (2025)'
];

static function load($setname=self::DEF_RULESETNAME) {
	if(!self::exists($setname)) $setname = self::DEF_RULESETNAME;
	return new ("\\App\\Libraries\\Mag\\Rulesets\\{$setname}");
}

static function exists($setname) {
	return isset(self::index[$setname]);
}
	
}
