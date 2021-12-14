<?php namespace App\Libraries\Mag;

class Rules {
	


static function index() {
	$retval = [];
	foreach(glob(__DIR__ . "/Rulesets/*.php") as $file) {
		$key = basename($file, '.php');
		$label = explode('_', $key, 2);
		$label[0] = strtoupper($label[0]);
		$retval[$key] = implode(' ', $label);
	}
	return $retval;
}

static function load($setname='Fig') {
	$file = __DIR__ . "/Rulesets/{$setname}.php";
	if(!file_exists($file)) $setname='Fig';
	$classname = "\\App\\Libraries\\Mag\\Rulesets\\{$setname}";
	return new $classname;
}
	
}