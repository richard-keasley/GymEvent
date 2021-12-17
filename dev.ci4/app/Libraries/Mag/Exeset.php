<?php namespace App\Libraries\Mag;

class Exeset {
public $data = [];
public $exercises = [];
public $ruleset = null;

public function __construct($post=[]) {
	foreach(['name','event','rulesetname'] as $key) {
		$this->data[$key] = $post[$key] ?? '';
	}
	$this->data['saved'] = date('Y-m-d H:i:s');
	$this->ruleset = \App\Libraries\Mag\Rules::load($this->rulesetname);

	foreach($this->ruleset->exes as $exekey=>$exe_rules) {
		switch($exe_rules['method']) {
			case 'tariff':
				$el_count = $this->ruleset->tariff['count'];
				$col_count = 3;
				break;
			case 'routine':
			default:
				$el_count = count($this->ruleset->routine['short']) - 1;
				$col_count = 3;
		}
		
		$element = array_fill(0, $col_count,'');
		$elements = array_fill(0, $el_count, $element);
		foreach($elements as $elnum=>$element) {
			foreach($element as $colnum=>$default) {
				$key = "{$exekey}_el_{$elnum}_{$colnum}";
				$elements[$elnum][$colnum] = $post[$key] ?? $default ;
			}
		}
		$this->exercises[$exekey]['elements'] = $elements;
		
		$neutrals = [];
		foreach(array_keys($exe_rules['neutrals']) as $nkey) {
			$key = "{$exekey}_n_{$nkey}";
			$neutrals[$nkey] = empty($post[$key]) ? 0  : 1;
		}
		$this->exercises[$exekey]['neutrals'] = $neutrals;	
	}
	}

public function __get($propname) {
	if(isset($this->data[$propname])) return $this->data[$propname];
}

}
