<?php namespace App\Libraries\Mag;

class Rules {
const version = '2021-12-11';
public $ruleset;
public $values;
public $short;
public $groups;

static function index() {
	$retval = [];
	foreach(glob(__DIR__ . "/rules/*.php") as $file) {
		$retval[] = basename($file, '.php');
	}
	return $retval;
}

function __construct($ruleset='fig') {
	$this->ruleset = $ruleset;
	$file = __DIR__ . "/rules/{$ruleset}.php";
	if(!file_exists($file)) {
		throw new \RuntimeException("Can't find MAG rule set $ruleset", 404);
	}
	include($file);
	foreach(['values', 'short'] as $prop) {
		if(!isset($$prop)) {
			throw new \RuntimeException("No $prop in MAG rule set '$ruleset'", 423);
		}
		$this->$prop = $$prop;
	}
	
	
}

	
}