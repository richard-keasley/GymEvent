<?php namespace App\Libraries\Mag\Rulesets;

class Ruleset {

protected $attributes = [];
protected $_exes = [];
protected $_routine = [];
protected $_tariff = [];

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
		'tariff' => $this->_tariff,
		default => $this->attributes[$key] ?? null
	};
}

public function select_options($params) {
	$options = [''];
	$arr = explode('.', $params);
	$propname = $arr[0] ?? null;
	$key = $arr[1] ?? null;
	if($propname) {
		$prop = $this->$propname ?? [];
		$arr = $key ? $prop[$key] ?? [] : $prop ;
		foreach($arr as $key=>$val) $options[$key] = $key;
	}
	return $options;
}

public function exe_options($exekey, $key) {
	$options = [''];
	$exe_rules = $this->exes[$exekey] ?? null;
	if($exe_rules) {
		$arr = $exe_rules[$key] ?? [];
		if(!$arr) {
			$params = "{$exe_rules['method']}.{$key}";
			return $this->select_options($params);
		}
		foreach($arr as $key=>$val) $options[$key] = $key;
	}
	return $options;
}

}
