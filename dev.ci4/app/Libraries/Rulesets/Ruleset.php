<?php namespace App\Libraries\Rulesets;

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
		'title' => \App\Libraries\Rulesets::title($name),
		'version' => null,
		'description' => null,
	];
}

public function __get($key) {
	switch($key) {
		case 'exes': 
		return $this->_exes;
		
		case 'routine': 
		return $this->_routine;
		
		case 'tariff': 
		return $this->_tariff;		
	}
	
	if(isset($this->attributes[$key])) {
		return $this->attributes[$key];
	}
	
	if(isset($this->_exes[$key])) {
		$exe_rules = $this->_exes[$key];
		$exe_method = $exe_rules['method'] ?? null;
		$default = $exe_method ? $this->$exe_method : [] ;
		return array_merge($default, $exe_rules);
	}
}

public function exe_options($exe_rules, $key) {
	$options = [''];
	$arr = $exe_rules[$key] ?? [] ;
	foreach($arr as $key=>$val) $options[$key] = $key;
	return $options;
}

public function discipline() {
	foreach(\App\Libraries\Rulesets::index as $discipline=>$rulesets) {
		if(isset($rulesets[$this->name])) {
			return $discipline;
		}
	}
	return null;
}

public function skills($exekey) {
	$exe_rules = $this->exes[$exekey] ?? null;
	if(!$exe_rules) return null;
	
	$dis = $this->discipline();
	$appvars = new \App\Models\Appvars();
	$appvar_id = strtolower("{$dis}.{$exekey}.skills");
	$appvar = $appvars->find($appvar_id);
	$retval['skills'] = $appvar ? $appvar->value : [] ;
	
	return [
		'key' => $exekey,
		'name' => $exe_rules['name'],
		'method' => $exe_rules['method'],
		'skills' => $appvar ? $appvar->value : [] 
	];
}

}
