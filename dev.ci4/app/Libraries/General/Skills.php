<?php namespace App\Libraries\General;

class Skills {
const attributes = ['hold', 'flexibility', 'strength', 'fs', 'afs'];
const blank = [
	'id' => 0,
	'description' => '',
	'group' => '',
	'difficulty' => '',
	'hold' => false,
	'flexibility' => false,
	'strength' => false,
	'fs' => false,
	'afs' => false
];
public $list = [];

function __construct($category, $composition=null) {
	$appvars = new \App\Models\Appvars();
	$arr = array_pad(explode('.', $category), 3, '');
	$appval = $appvars->get_value("general.{$arr[0]}.skills");
	if($appval) {
		foreach($appval as $sk_id=>$skill) {
			if($composition) {
				$group = $skill['group'];
				if(stripos($composition[$group], $skill['difficulty'])!==false) {
					$this->list[$sk_id] = $skill;
				}
			}
			else $this->list[$sk_id] = $skill;
		}
	}
}

function get($id) {
	return isset($this->list[$id]) ? $this->list[$id] : self::blank ;
}

private $_grouped = null;
function get_grouped() {
	if(!$this->_grouped) {
		$retval = [];
		foreach($this->list as $sk_id=>$skill) {
			$retval[$skill['group']][$skill['difficulty']][$sk_id] = $skill;
		}
		ksort($retval);
		foreach(array_keys($retval) as $grp_id) ksort($retval[$grp_id]);
		$this->_grouped = $retval;
	}
	return $this->_grouped;
}

static function match($skill, $pattern) {
	if(!$skill) return false;
	// check correct group AND difficulty
	foreach(['group','difficulty'] as $key) {
		if(!empty($pattern[$key])) {
			if(stripos($pattern[$key], $skill[$key])===false) return false;
		}
	}
	// skill OK if it has ANY attributes in pattern
	foreach(self::attributes as $key) {
		if(!empty($pattern[$key])) {
			if($skill[$key]) return true;
		}
	}
	return false;
} 

} 