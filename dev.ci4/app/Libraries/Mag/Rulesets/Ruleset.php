<?php namespace App\Libraries\Mag\Rulesets;

class Ruleset {

protected $attributes = [];
protected $_exes = [];
protected $_routine = [];
protected $_tarrif = [];

public function __construct() {
	$classname = get_class($this);
	$arr = explode('\\', $classname);
	$name = array_pop($arr);
	$this->attributes = [
		'name' => $name,
		'title' => \App\Libraries\Mag\Rules::index[$name],
		'version' => null,
		'description' => null,
	];
}

public function __get($key) {
	return match($key) {
		'exes' => $this->_exes,
		'routine' => $this->_routine,
		'tarrif' => $this->tarrif,
		default => $this->attributes[$key] ?? null
	};
}

public function select_options($params) {
	$options = [''];
	
	$arr = explode('.', $params);
	$propname = $arr[0] ?? null;
	if($propname) {
		$prop = $this->$propname ?? [];
		$key = $arr[1] ?? null;
		$arr = $key ? $prop[$key] ?? [] : $prop ;
		foreach(array_keys($arr) as $key) {
			$options[$key] = $key;
		}
	}
	return $options;
}



}
