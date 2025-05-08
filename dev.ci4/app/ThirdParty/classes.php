<?php namespace App\ThirdParty;

class classes {

static function classes() {
	// expected path: publisher\package = package-version
	$retval = [];
	$search = __DIR__ . DIRECTORY_SEPARATOR;
	$strlen = strlen($search);
	$config = new \Config\Autoload;
	foreach($config->psr4 as $name=>$path) {
		if(strpos($path, $search)!==0) continue;
		$path = substr($path, $strlen);
		$path = explode(DIRECTORY_SEPARATOR, $path);
		if(!$path) continue;
		
		$path = $path[0];
		$arr = explode('-', $path);
		$version = end($arr);
		
		$arr = explode('\\', $name);
		$title = end($arr);
		# d($path, $name, $title, $version);
		
		$retval[$title] = sprintf('<span title="%s">%s</span>', $name, $version);
	}
	
	return $retval;
}

}
