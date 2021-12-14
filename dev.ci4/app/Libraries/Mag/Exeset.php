<?php namespace App\Libraries\Mag;

class Exeset {
public $data = [];
public $exercises = [];
public $ruleset = null;

public function __construct($post=[]) {
	foreach(['name','event','rulesetname','saved'] as $key) {
		$this->data[$key] = $post[$key] ?? '';
	}
	$this->ruleset = \App\Libraries\Mag\Rules::load($this->rulesetname);

	foreach($this->ruleset->exes as $exekey=>$exe_rules) {
		switch($exe_rules['method']) {
			case 'tariff':
				$el_count = $this->ruleset->tariff['count'];
				$col_count = 3;
				break;
			case 'routine':
			default:
				$el_count = $this->ruleset->routine['count'];
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

public function exeval($exekey) {
	$retval = [
		'val' => 0
	];
	if(empty($this->ruleset->exes[$exekey])) return $retval;
	if(empty($this->exercises[$exekey])) return $retval;
	
	$exe_rules = $this->ruleset->exes[$exekey];
	$exercise = $this->exercises[$exekey];
	
	switch($exe_rules['method']) {
		case 'tariff':
			break;
		case 'routine':
		default:
			$retval_groups = [];
			foreach($exercise['elements'] as $element) {
				$letter = $element[0];
				$value = $this->ruleset->routine['values'][$letter] ?? 0 ;
				if($value) {
					$retval['val'] += $value ;
					$group_num = $element[1];
					$group_val = $this->ruleset->routine['group_vals'][$group_num] ?? null ;
					if($group_val) {
						foreach($group_val as $el_letter=>$grp_worth) {
							$el_value = $this->ruleset->routine['values'][$el_letter];
							if($value>=$el_value) $retval_groups[$group_num] = $grp_worth;
						}
					}				
				}
			}
			$retval['EGR'] = array_sum($retval_groups);
			
			$retval['pen'] = 0 ;
			foreach($exercise['neutrals'] as $nkey=>$nval) { 
				if(!$nval) {
					$neutral = $exe_rules['neutrals'][$nkey]; 
					$retval['pen'] -= $neutral['deduction'];
				}
			}
			// end routine
	}
	
	
	# d($exe_rules);
	# d($exercise);
	
	return $retval;	
	

	
}

}
