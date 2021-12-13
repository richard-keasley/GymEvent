<?php namespace App\Libraries\Mag;

class Rules {

static function index() {
	$retval = [];
	foreach(glob(__DIR__ . "/Rulesets/*.php") as $file) {
		$retval[] = basename($file, '.php');
	}
	return $retval;
}

static function load($setname='Fig') {
	$file = __DIR__ . "/Rulesets/{$setname}.php";
	if(!file_exists($file)) return null;
	$classname = "\\App\\Libraries\\Mag\\Rulesets\\{$setname}";
	return new $classname;
}
	
}