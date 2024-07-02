<?php namespace App\Libraries\Mag;

class Exeset {
const filter = [
	'<' => '{',
	'>' => '}',
	'&' => '+'
];

public $data = [];
public $exercises = [];
public $ruleset = null;

static function sanitize($arr) {
	foreach($arr as $key=>$val) {
		$arr[$key] = is_array($val) ? 
			self::sanitize($val) : 
			strtr(trim($val), self::filter) ;
	}
	return $arr;
}

public function __construct($post=[]) {
	// sanitize
	$post = self::sanitize($post);
	/*
	foreach($post as $key=>$val) {
		$post[$key] = strtr(trim($val), self::filter);
	}
	*/
	foreach(['name', 'event', 'rulesetname'] as $key) {
		$this->data[$key] = $post[$key] ?? '';
	}
	$this->data['saved'] = date('Y-m-d H:i:s');
	$this->ruleset = \App\Libraries\Mag\Rules::load($this->rulesetname);

	foreach($this->ruleset->exes as $exekey=>$exe_rules) {
		switch($exe_rules['method']) {
			case 'tariff':
				$el_count = $exe_rules['exe_count'];
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
		
		if(!empty($exe_rules['connection'])) {
			$key = "{$exekey}_con";
			$val = $post[$key] ?? 0 ;
			$this->exercises[$exekey]['connection'] = floatval($val);	
		}
		
		$neutrals = [];
		foreach(array_keys($exe_rules['neutrals']) as $nkey) {
			$key = "{$exekey}_nd_{$nkey}";
			$neutrals[$nkey] = empty($post[$key]) ? 0  : 1;
		}
		$this->exercises[$exekey]['neutrals'] = $neutrals;	
	}
	}

public function __get($propname) {
	if(isset($this->data[$propname])) return $this->data[$propname];
}

}
