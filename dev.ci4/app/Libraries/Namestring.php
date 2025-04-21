<?php namespace App\Libraries;

/* 
a comma separated string containing:
name1, name2, mem_num, DoB
Only used in \app\entity\clubret
Each item in "participants" contains an array of namestring
Each item in "staff" contains a single namestring

See views/admin/setup/test/namestring
*/

class Namestring implements \Stringable {

const hint = '<span class="bg-primary-subtle">Name1, Name2, Membership No., Date of birth (dd/mm/yy)</span>';

private $values = ['name1'=>'', 'name2'=>'', 'mem_num'=>0, 'dob'=>''];
private $attribs = [
	'dt' => null, // datetime for DoB
	'input' => null, // original request
	'error' => null,
];

function __construct($input) {
	$input = filter_string($input);
	$this->attribs['input'] = $input;
	
	if(!$input) {
		$this->attribs['error'] = "is empty";
		return;
	}
	
	$fragments = preg_split("/ *[\t,] */", $input);
	if(count($fragments)<2) {
		$this->attribs['error'] = "has missing commas";
		return;
	}
	
	foreach(array_keys($this->values) as $rkey) {
		foreach($fragments as $ikey=>$fragment) {
			if($fragment && !$this->values[$rkey]) {
				$test = $this->sanitize($rkey, $fragment);
				if($test) {
					$this->values[$rkey] = $test;
					$fragments[$ikey] = null; // stop it being used again
				}
			}
		}			
	}
	
	// no separator between names
	if($this->values['name1'] && !$this->values['name2']) {
		$names = explode(' ', $this->values['name1']);
		$last = array_key_last($names);
		if($last) {
			$this->values['name1'] = implode(' ', array_slice($names, 0, $last));
			$this->values['name2'] = $names[$last];
			# d($this->values);
		}
	}
	
	$this->attribs['error'] = $this->error();
}

function __get($key) {
	if(isset($this->values[$key])) return $this->values[$key];
	if(isset($this->attribs[$key])) return $this->attribs[$key];
	if($key=='name') return "{$this->name1} {$this->name2}";
	return null;
}

function __toString(): string {
	return ($this->error) ? 
		$this->input : 
		implode(', ', $this->values) ;
}

function __toArray() {
	return $this->values;
}

private function error() {	
	if(!$this->name1 && !$this->name2) return "has no name"; 
	if(!$this->name1 || !$this->name2) return "only has one name"; 
	if(strlen($this->name)<6) return "has invalid name";
	
	if(!$this->mem_num)  return "has no membership number"; 

	if(!$this->dob) return "has no DoB";
	if(!$this->dt) return "has invalid DoB";	
	if($this->dt>self::calc('dob_max')) return "is too young";
	if($this->dt<self::calc('dob_min')) return "has invalid DoB";
		
	// no error
	return null;
}

private function sanitize($filter, $val) {
	switch($filter) {
		case 'dob':
		$strip = str_split('()<>{}~#@,:;');
		$val = str_replace($strip, '', $val);
		$this->attribs['dt'] = self::get_dt($val);
		return $this->dt ? $this->dt->format('j-M-Y') : null ;
			
		case 'mem_num':
		$val = (int) $val;
		return $val ? (int) $val : null ;
			
		case 'name1':
		case 'name2': 
		$pattern = '#^[a-z][a-z\']#i';
		return preg_match($pattern, $val) ? $val : null ;
		
		default:
		return null;
	};
}

static function get_dt($str) {
	if(!$str) return null;
	try {
		$formats = [
			'd/m/Y', // standard UK
			'd-m-Y', 
			'd.m.Y', // German
			'd m Y', 
			false // create from string
		];
		foreach($formats as $format) {
			# d($format);
			$dt = $format ? 
				\DateTime::createFromFormat($format, $str) : 
				new \DateTime($str) ;
			if($dt) break;
		}
		
		// strip time from datetime
		$dt->setTime(0,0);
		// check for 2 digit year	
		$yr = (int) $dt->format('Y');
		if($yr<100) {
			// 2 digit year, bring it to this century
			$add = $yr>self::calc('now_yr') ? 1900 : 2000 ;
			$dt->add(new \DateInterval("P{$add}Y"));
		}
		
		return $dt;
	}
	catch(\throwable $ex) {
		# d($ex);
		return null;
	}
	
	
}

static $calc = null;
static function calc($key) {
	if(!self::$calc) {
		$calc = [];
		$now = new \DateTimeImmutable;
		$calc['now'] = $now;
		$period = new \DateInterval("P5Y");
		$calc['dob_max'] = $now->sub($period);
		$period = new \DateInterval("P90Y");
		$calc['dob_min'] = $now->sub($period);
		$calc['now_yr'] = (int) $now->format('y');
		self::$calc = $calc;
	}
	return self::$calc[$key];
}

}