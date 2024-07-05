<?php namespace App\Libraries\Ma2;

use \App\Libraries\Mag\Rules;

class Exeset {
const filter = [
	'<' => '{',
	'>' => '}',
	'&' => '+'
];

static function sanitize($arr) {
	foreach($arr as $key=>$val) {
		$arr[$key] = is_array($val) ? 
			self::sanitize($val) : 
			strtr(trim($val), self::filter) ;
	}
	return $arr;
}

public $data = [];
public $exercises = [];
public $ruleset = null;

public function __construct($request=[]) {
	// sanitize
	$request = self::sanitize($request);
	
	$this->data = [
		'name' => $request['name'] ?? '',
		'event' => $request['event'] ?? '',
		'rulesetname' => $request['rulesetname'] ?? '#',
		'saved' => date('Y-m-d H:i:s')
	];
	
	if(!Rules::exists($this->data['rulesetname'])) {
		$this->data['rulesetname'] = Rules::DEF_RULESETNAME;
	}
	$this->ruleset = Rules::load($this->data['rulesetname']);
	
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
				$elements[$elnum][$colnum] = $request[$exekey]['elements'][$elnum][$colnum] ?? $default ;
			}
		}
		$this->exercises[$exekey]['elements'] = $elements;
		
		if(!empty($exe_rules['connection'])) {
			$val = (float) ($request[$exekey]['connection'] ?? 0);
			while($val > 4) { $val = $val / 10; }
			$this->exercises[$exekey]['connection'] = number_format($val, 1);	
		}
		
		$neutrals = [];
		foreach(array_keys($exe_rules['neutrals']) as $nkey) {
			$val = $request[$exekey]['neutrals'][$nkey] ?? 0 ;
			$neutrals[$nkey] = $val ? 1 : 0;
		}
		$this->exercises[$exekey]['neutrals'] = $neutrals;	
	}
	}

public function __get($propname) {
	if(isset($this->data[$propname])) return $this->data[$propname];
}

}
