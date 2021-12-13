<?php namespace App\Libraries\Mag;

class Routineset {
	
public $data = [
	'name' => '',
	'event' => '',
	'rulesetname' => '',
	'saved' => ''
];
public $routines = [];

public function __construct($data=[]) {
	foreach($this->data as $key=>$val) {
		if(isset($data[$key])) $this->data[$key] = $data[$key];
	}
	foreach(\App\Libraries\Mag\Rules::apparatus as $abbr=>$label) {
		$this->routines[$abbr] = isset($data[$abbr]) ? $data[$abbr] : ['elements'=>[], 'neutral'=>[]];	
	}
}

public function __get($propname) {
	if($propname=='ruleset') {
		return \App\Libraries\Mag\Rules::load($this->data['rulesetname']);
	}
	if(isset($this->data[$propname])) return $this->data[$propname];
}

}