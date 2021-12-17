<?php namespace App\Libraries\Mag;
// helper load rulesets

class Rules {

static function index() {
	$retval = [];
	foreach(glob(self::filepath('*')) as $file) {
		$key = basename($file, '.php');
		$label = explode('_', $key, 2);
		$label[0] = strtoupper($label[0]);
		$retval[$key] = implode(' ', $label);
	}
	return $retval;
}

static function load($setname='Fig') {
	$filepath = self::filepath($setname);
	if(!file_exists($filepath)) $setname='Fig';
	$classname = "\\App\\Libraries\\Mag\\Rulesets\\{$setname}";
	return new $classname;
}

static function filepath($setname='Fig') {
	return  __DIR__ . "/Rulesets/{$setname}.php";
}
	
}