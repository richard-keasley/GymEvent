<?php namespace App\Libraries\General;

class Intention { 
const version = '2021-05-14';

const filter = [
	'name' => [
		'filter' => FILTER_SANITIZE_STRING,
		'flags' => FILTER_FLAG_NONE,
		'default' => 'name'
	],
	'gender' => [
		'filter' => FILTER_SANITIZE_STRING,
		'flags' => FILTER_FLAG_NONE,
		'default' => 'female'
	],
	'dob' => [
		'filter' => FILTER_SANITIZE_STRING,
		'flags' => FILTER_FLAG_NONE,
		'default' => '2000-01-01'
	],
	'exercise' => [
		'filter' => FILTER_SANITIZE_STRING,
		'flags' => FILTER_FLAG_NONE,
		'default' => 'fx'
	],
	'level' => [
		'filter' => FILTER_SANITIZE_STRING,
		'flags' => FILTER_FLAG_NONE,
		'default' => 'novice'
	],
	'skills' => [
		'filter' => FILTER_VALIDATE_INT,
		'flags' => FILTER_FORCE_ARRAY,
		'default' => []
	],
	'specials' => [
		'filter' => FILTER_VALIDATE_INT,
		'flags' => FILTER_FORCE_ARRAY,
		'default' => []
	],
	'bonuses' => [
		'filter' => FILTER_VALIDATE_INT,
		'flags' => FILTER_FORCE_ARRAY,
		'default' => []
	]
];

// read from appvars
public $category = '';
public $rules = null;
public $data = []; // as supplied by user

static function versions() {
	return [
		'Rules' => \App\Libraries\General\Rules::version,
		'Intention' => \App\Libraries\General\Intention::version,
		'Updated' => date('Y-m-d')
	];
}
	
static function decode($json) {
	// read input
	$arr = json_decode($json, 1);
	if(!is_array($arr)) $arr = [];
	$data = filter_var_array($arr, self::filter);
	foreach(self::filter as $key=>$filter) {
		if(!$data[$key]) $data[$key] = $filter['default'];
	}
	// read only fields	
	$data['v_rules'] = \App\Libraries\General\Rules::version;
	$data['v_program'] = self::version;
	$data['v_updated'] = date('Y-m-d');
	
	// create intention
	$category = implode('.', [$data['exercise'], $data['level'], $data['gender']]);
	$retval = new self($category);
	$count = $retval->rules->composition['skill_count'];
	
	// ensure correct number of skills for these rules
	foreach(['skills', 'specials', 'bonuses'] as $key) {
		$data[$key] = array_pad($data[$key], $count, 0);
		$data[$key] = array_slice($data[$key], 0, $count);
	}
	
	$retval->data = $data;
	return $retval;
}

function __construct($category='') {
	$this->category = $category;
	$this->rules = new \App\Libraries\General\Rules($category);
}

function encode() {
	return json_encode($this->data);
}

function __get($prop) {
	return isset($this->data[$prop]) ? $this->data[$prop] : null ;
}

private $sv = [
	'routine' => ['skills'=>[], 'specials'=>[], 'bonuses'=>[]],
	'values' => [],
	'errors' => []
];

function sv_table() {
	/* returns a table showing how start value is calculated */
	helper('inflector');
	$difficulties = ['A','B','C','D','E'];
	$this->sv['errors'] = [];
		
	// load routine
	foreach($this->data['skills'] as $sk_num=>$sk_id) {
		$this->sv['routine']['skills'][$sk_num] = $this->rules->skills->get($sk_id);
		$this->sv['routine']['specials'][$sk_num] = $this->rules->specials->get($this->data['specials'][$sk_num]);
		$this->sv['routine']['bonuses'][$sk_num] = $this->rules->bonuses->get($this->data['bonuses'][$sk_num]);
	}
	// ignore repeated
	$done = ['skills'=>[], 'specials'=>[], 'bonuses'=>[]];
	foreach($this->sv['routine']['skills'] as $sk_num=>$skill) {
		if($skill['id']) {
			if(in_array($skill['id'], $done['skills'])) {
				$this->sv['errors'][] = sprintf("'%s' has been repeated", $skill['description']);
				$this->sv['routine']['skills'][$sk_num] = \App\Libraries\General\Skills::blank;
			}
			else $done['skills'][] = $skill['id'];
		}
		$special = $this->sv['routine']['specials'][$sk_num];
		if($special['id']) {
			if(in_array($special['id'], $done['specials'])) {
				$this->sv['errors'][] = sprintf("'%s' has been repeated", $special['description']);
				$this->sv['routine']['specials'][$sk_num] = \App\Libraries\General\Specials::blank;
			}
			else $done['specials'][] = $special['id'];
		}
		$bonus = $this->sv['routine']['bonuses'][$sk_num];
		if($bonus['id']) {
			if(in_array($bonus['id'], $done['bonuses'])) {
				$this->sv['errors'][] = sprintf("'%s' has been repeated", $bonus['description']);
				$this->sv['routine']['bonuses'][$sk_num] = \App\Libraries\General\Bonuses::blank;
			}
			else $done['bonuses'][] = $bonus['id'];
		}
	} 
	// ignore skills if too many from a group
	$done = array_fill(1, 4, 0);
	$max = $this->rules->composition['max_grp'];
	foreach($this->sv['routine']['skills'] as $sk_num=>$skill) {
		$grp_id = $skill['group'];
		if(isset($done[$grp_id])) {
			$done[$grp_id]++;
			if($done[$grp_id]>$max) {
				$this->sv['routine']['skills'][$sk_num] = \App\Libraries\General\Skills::blank;
			}
		}
	}
	// check enough skills from each groups 
	$min = $this->rules->composition['min_grp'];
	foreach($done as $group=>$count) {
		if($count<$min) {
			// not enough
			$this->sv['errors'][] = counted($min, sprintf('x group %s skill', $group)) . ' needed';
		}
	}
		
	// get values
	$values_blank = array_fill(0, count($this->sv['routine']['skills']), null);
	$this->sv['values'] = ['difficulties'=>$values_blank, 'specials'=>$values_blank, 'bonuses'=>$values_blank];
	// difficulty
	$downgrade_max = 2;
	$downgrade_count = 0;
	foreach($difficulties as $difficulty) {
		$max = $this->rules->composition[$difficulty];
		$done = 0;
		$downgrade = $difficulty;
		$downgrade ++;	
		// same difficulty without bonus
		foreach($this->sv['routine']['skills'] as $sk_num=>$skill) {
			if($done<$max && !$this->sv['values']['difficulties'][$sk_num] && $skill['difficulty']==$difficulty && !$this->data['bonuses'][$sk_num]) {
				$this->sv['values']['difficulties'][$sk_num] = $difficulty;	
				$done++;
			}
		}
		// higher difficulty without bonus
		foreach($this->sv['routine']['skills'] as $sk_num=>$skill) {
			if($done<$max && !$this->sv['values']['difficulties'][$sk_num] && $skill['difficulty']==$downgrade && $downgrade_count<$downgrade_max && !$this->data['bonuses'][$sk_num]) {
				$this->sv['values']['difficulties'][$sk_num] = $difficulty;	
				$done++;
				$downgrade_count++;
			}
		}
		// same difficulty ignoring bonus
		foreach($this->sv['routine']['skills'] as $sk_num=>$skill) {
			if($done<$max && !$this->sv['values']['difficulties'][$sk_num] && $skill['difficulty']==$difficulty) {
				$this->sv['values']['difficulties'][$sk_num] = $difficulty;
				$done++;
			}
		}
		// higher difficulty ignoring bonus
		foreach($this->sv['routine']['skills'] as $sk_num=>$skill) {
			if($done<$max && !$this->sv['values']['difficulties'][$sk_num] && $skill['difficulty']==$downgrade && $downgrade_count<$downgrade_max) {
				$this->sv['values']['difficulties'][$sk_num] = $difficulty;	
				$done++;
				$downgrade_count++;
			}
		}
		if($done<$max) {
			// not enough
			/* sorting skills by low to high difficulty gets an accurate result if all skills present. However, if downgrades are used and there are still missing skills, skills will have been given a lower start value and the wrong error is displayed. 
			Solution... recalculate without downgrades if there are not enough skills! */
			$this->sv['values']['difficulties'] = $values_blank;
			foreach($difficulties as $difficulty) {
				$max = $this->rules->composition[$difficulty];
				$done = 0;
				// same difficulty without bonus
				foreach($this->sv['routine']['skills'] as $sk_num=>$skill) {
					if($done<$max && !$this->sv['values']['difficulties'][$sk_num] && $skill['difficulty']==$difficulty) {
						$this->sv['values']['difficulties'][$sk_num] = $difficulty;	
						$done++;
					}
				}
				if($done<$max) $this->sv['errors'][] = counted($max, sprintf('x %s skill', $difficulty)) . ' needed';
			}
			break; // stop all checks
		}
	}
	
	// specials
	$done = [];
	foreach($this->sv['routine']['specials'] as $sk_num=>$special) {
		$done[$sk_num] = $special['id'];
	}
	foreach($this->rules->specials->list as $special) {
		$description = sprintf("<abbr title=\"Special requirement\">SR</abbr> <em>'%s'</em>", $special['description']);
		$sk_num = array_search($special['id'], $done);
		if($sk_num===false) { // missing
			$this->sv['errors'][] = $description . ' needed';
		}
		else { // check it
			if($this->match($sk_num, $special)) {
				$this->sv['values']['specials'][$sk_num] = $special['id'];	
			}
			else {
				$this->sv['errors'][] = $description . ' is invalid';
			}
		}
	}
	
	// bonuses
	if(!$this->sv['errors'] || 1) {
		// check
		foreach($this->sv['routine']['bonuses'] as $sk_num=>$bonus) {
			if($bonus['id']) {
				$description = sprintf("Bonus '<em>%s</em>'", $bonus['description']);
				// check not been used by difficulty
				if($this->sv['values']['difficulties'][$sk_num]) {
					$this->sv['errors'][] = $description . ' used in difficulty';
				}
				else {
					if($this->match($sk_num, $bonus)) {
						$this->sv['values']['bonuses'][$sk_num] = $bonus['id'];	
					}
					else {
						$this->sv['errors'][] = $description . ' is invalid';
					}
				}
			}
		}
	}
	
	// calculate value table
	$worth = ['A'=>.2,'B'=>.3,'C'=>.5,'D'=>.7,'E'=>.8];
	foreach($this->sv['values']['difficulties'] as $sk_num=>$difficulty) {
		$value = isset($worth[$difficulty]) ? $worth[$difficulty] : 0 ;
		$this->sv['values']['difficulties'][$sk_num] = $value;	
	}
	// specials
	$sr_val = $this->rules->composition['sr_val'];
	$sr_count = count($this->rules->specials->list);
	$sr_value = $sr_count ? $sr_val / $sr_count : 0;
	foreach($this->sv['values']['specials'] as $sk_num=>$special_id) {
		$value = $special_id ? $sr_value : 0 ;
		$this->sv['values']['specials'][$sk_num] = $value;	
	}
	// bonuses
	foreach($this->sv['values']['bonuses'] as $sk_num=>$bonus_id) {
		if($bonus_id) {
			$bonus = $this->rules->bonuses->list[$bonus_id];
			$difficulty = $bonus['difficulty'];
			$value = isset($worth[$difficulty]) ? $worth[$difficulty] : 0 ;
		}
		else $value = 0 ;
		$this->sv['values']['bonuses'][$sk_num] = $value;	
	}
	
	return $this->sv;	
}

function match($sk_num, $pattern) {
	if($pattern['type']) {
		$arr = explode('=', $pattern['type'], 2);
		$class = __NAMESPACE__ . '\Intention';
		$fn = "match_{$arr[0]}";
		$params = [];
		if(isset($arr[1])) {
			foreach(explode(';', $arr[1]) as $row) {
				$val = explode(':', $row, 2);
				if(count($val)==2) $params[$val[0]] = $val[1];
			}
		}
		if(method_exists($class, $fn)) {
			return call_user_func([$class, $fn], $sk_num, $pattern, $params);
		}
	}
	return \App\Libraries\General\Skills::match($this->sv['routine']['skills'][$sk_num], $pattern);
}

function match_series($sk_num, $pattern, $params=[]) {
	// split pattern keys on commas
	$pattern1 = $pattern;
	$pattern2 = $pattern;
	$keys = array_merge(['group','difficulty'], \App\Libraries\General\Skills::attributes);
	foreach($pattern as $key=>$val) {
		if(in_array($key, $keys)) {
			$val = explode(',', $val);
			$pattern1[$key] = $val[0];
			$pattern2[$key] = empty($val[1]) ? '' : $val[1];
		}
	}
	// find this skill, next and previous
	$n = $sk_num - 1;
	$prev_skill = isset($this->sv['routine']['skills'][$n]) ? $this->sv['routine']['skills'][$n] : null;
	$n++;
	$this_skill = isset($this->sv['routine']['skills'][$n]) ? $this->sv['routine']['skills'][$n] : null;
	$n++;
	$next_skill = isset($this->sv['routine']['skills'][$n]) ? $this->sv['routine']['skills'][$n] : null;
	// if(this==pattern1) { if(prev==pattern2 OR next==pattern2) return true }
	if(\App\Libraries\General\Skills::match($this_skill, $pattern1)) {
		if(\App\Libraries\General\Skills::match($prev_skill, $pattern2)) return true;
		if(\App\Libraries\General\Skills::match($next_skill, $pattern2)) return true;
	}
	if(\App\Libraries\General\Skills::match($this_skill, $pattern2)) {
		if(\App\Libraries\General\Skills::match($prev_skill, $pattern1)) return true;
		if(\App\Libraries\General\Skills::match($next_skill, $pattern1)) return true;
	}
	return false;
}

function match_gx($match_num, $pattern, $params=[]) {
	// group excluder
	// checks not same group as other skills in the routine
	$match_skill = $this->sv['routine']['skills'][$match_num];
	if(!\App\Libraries\General\Skills::match($match_skill, $pattern)) return false;

	foreach($params as $param_key=>$param_val) {
		switch($param_key) {
			case 'bonus':
			// not the same group as bonus id specified
			foreach($this->sv['values']['bonuses'] as $sk_num=>$bonus_id) {
				if($bonus_id==$param_val) {
					if($match_skill['group']==$this->sv['routine']['skills'][$sk_num]['group']) {
						$this->sv['errors'][] = sprintf('Bonus %u has the same group as bonus %u', $match_num+1, $sk_num+1);
						return false;
					}
				}
			}
			break;

			case 'difficulty':
			// not same group as any skill used as difficulty specified
			$match_dif = strtoupper($param_val);
			foreach($this->sv['values']['difficulties'] as $sk_num=>$difficulty) {
				if($difficulty==$match_dif) {
					if($match_skill['group']==$this->sv['routine']['skills'][$sk_num]['group']){
						$this->sv['errors'][] = sprintf('Bonus %u has the same group as skill %u', $match_num+1, $sk_num+1);
						return false;
					}
				}
			}
			break;
		}
	}
	return true;
}

}

