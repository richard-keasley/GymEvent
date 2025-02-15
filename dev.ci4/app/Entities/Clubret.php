<?php namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Clubret extends Entity {

protected $casts = [
	'user_id' => 'integer',
	'event_id' => 'integer',
	'stafffee' => 'integer'
];

static function enabled() {
	return \App\Libraries\Auth::check_path('clubrets', 0) != 'disabled';
}

public function getStaff() {
	$db_val = filter_json($this->attributes['staff'] ?? null);
	$entity_val = [];
	foreach($db_val as $db_row) {
		if(empty($db_row['cat'])) $db_row['cat'] = '';
		if(empty($db_row['name'])) $db_row['name'] = '';
		$entity_val[] = [
			'cat' => $db_row['cat'],
			'name' => $db_row['name']
		];
	}
	return $entity_val;
}

public function setStaff($arr) {
	$staff = [];
	foreach($arr as $row) {
		$namestring = new \App\Entities\namestring($row['name']);
		$row['name'] = (string) $namestring;
		$staff[] = $row;
	}
	$this->attributes['staff'] = json_encode($staff);
	return $this;
}
	
private $_event = null;
public function event() {
	if(!$this->_event) {
		$model = new \App\Models\Events();
		$this->_event = $model->withDeleted()->find($this->event_id);
	}
	return $this->_event;
}

private $_user = null;
public function user() {
	if(!$this->_user && $this->user_id) {
		$model = new \App\Models\Users();
		$this->_user = $model->withDeleted()->find($this->user_id);
	}
	return $this->_user;
}

function breadcrumb($method='view', $controller='') {
	if($method=='view') {
		$user = $this->user();
		$label = $user ? $user->name : '[not found]';
	}
	else $label = $method;
	return [$this->url($method, $controller), $label];
}

function url($method='', $controller='') {
	$arr = [];
	if($controller) $arr[] = $controller;
	$arr[] = 'clubrets';
	if($method) $arr[] = $method;
	$arr[] = $this->event_id;
	$arr[] = $this->user_id;
	return implode('/', $arr);
}

public $errors = [];
function errors($err_type=null) {
	$ret = '';
	foreach($this->errors as $type=>$errors) {
		if(!$err_type || $err_type===$type) {
			$text1 = $err_type ? '' : "$type: ";
			foreach($errors as $error) {
				$ret .= sprintf('<li class="list-group-item list-group-item-danger">%s%s</li>', $text1, $error);
			}
		}
	}
	return $ret ? sprintf('<ul class="list-group">%s</ul>', $ret) : '' ;
}

public function getParticipants() {
	$db_val = filter_json($this->attributes['participants'] ?? null);
	$entity_val = []; $arr = [];
	foreach($db_val as $row) {
		foreach(['dis', 'cat', 'team', 'names', 'opt'] as $key) {
			if(empty($row[$key])) $row[$key]='';
		}
		$entity_val[] = [
			'dis' => $row['dis'],
			'cat' => csv_array($row['cat']),
			'team' => $row['team'],
			'names' => explode("\n", $row['names']),
			'opt' => $row['opt']
		];
	}
	return $entity_val;
}

public function setParticipants($arr) {
	$participants = [];
	foreach($arr as $row) {
		$row_names = [];
		foreach($row['names'] as $name) {
			$namestring = new \App\Entities\namestring($name);
			if($namestring->name) $row_names[] = (string) $namestring;
		}
		if($row_names) {
			$row['names'] = implode("\n", $row_names);
			$row['cat'] = implode(',', $row['cat']);
			$participants[] = $row;
		}	
	}
	$this->attributes['participants'] = json_encode($participants);
	return $this;
}

public function check() {
	$event = $this->event();
	
	$this->errors = [];
	$errors = [];
	if($this->participants) { 
		foreach($this->participants as $rowkey=>$row) {
			$row_error = '';
			if(empty($row['dis'])) $row_error = "has invalid discipline";
			if(empty($row['cat'])) $row_error = "has invalid category";
			foreach($row['names'] as $name) {
				$namestring = new \App\Entities\namestring($name);
				$name_error = $namestring->error;
				if($name_error) $row_error = $name_error;
			}
			if($row_error) $errors[] = sprintf('row %u %s', $rowkey+1, $row_error);
		}
	}
	else {
		$errors[] = "No participants entered in this return";
	}
	
	if($event->terms && !$this->terms) {
		$errors[] = "Clubs entries will only be accepted after they agree to the terms of this event";
	}
	
	if($errors) $this->errors['participants'] = $errors;
	
	if(!empty($event->staffcats[0])) {
		$errors = [];
		if($this->staff) {
			foreach($this->staff as $rowkey=>$row) {
				$namestring = new \App\Entities\namestring($row['name']);
				$error = $namestring->error;
				if($error) $errors[] = sprintf('row %u %s', $rowkey+1, $error);
			}
		}
		else {
			$errors[] = "No staff entered in this return";
		}
		if($errors) $this->errors['staff'] = $errors;
	}
		
	#d($this->errors);
	return $this->errors ? false : true ;
}

static function discat_sort($discats, $dis, $cat) {
	$ret = [];
	foreach($discats as $discat) {
		if($discat['name']==$dis) {
			$cats = array_pad($cat, count($discat['cats']), '');
			foreach($discat['cats'] as $key=>$catrow) {
				$pos = array_search($cats[$key], $catrow);
				if($pos===false) $pos = 99;
				$ret[] = sprintf("%02d", $pos); 
			}
		}
	}
	$ret = implode('-', $ret);
	return $ret;
}

public function fees($op=1) {
	$event = $this->event();
	
	$fees = []; $evt_fees = []; 
	foreach($this->participants as $participant) {
		$dis = $participant['dis'];
		if(empty($evt_fees[$dis])) {
			$evt_fees[$dis] = [
				'gymnast' => $event->discat_inf($dis, 'fg'),
				'entry' => $event->discat_inf($dis, 'fe')
			];
		}
		$count = count($participant['names']);
		$fee = $count * $evt_fees[$dis]['gymnast'] + $evt_fees[$dis]['entry'];
		if(!isset($fees[$dis])) $fees[$dis] = [$dis, 0];
		$fees[$dis][1] += $fee;
	}
	
	if($event->stafffee && !$this->stafffee) {
		$fees['_stafffee'] = ['Staff', $event->stafffee];
	}
		
	if($op=='fees') return $fees;
	
	$total = array_sum(array_column($fees, 1));
	
	if($op=='htm') {
		$vartable = new \App\Views\Htm\Vartable;
		foreach($fees as $fee) {
			$vartable->items[$fee[0]] = \App\Views\Htm\Table::money($fee[1]);
		}
		$vartable->footer = [\App\Views\Htm\Table::money($total), 'Total'];
		$retval = $vartable->htm();
		
		if($event->stafffee && !$this->stafffee) {
			$retval .= sprintf('<p class="text-bg-light fw-bold">&pound;%1.2f has been added to you entry fee to cover staff costs for this event', $event->stafffee);
		}
		
		return $retval;
	}
	
	return $total;
}

}

class namestring implements \Stringable {
/* 
a namestring is a comma separated string containing:
name 1 name 2, DoB
Only used in entity "clubret"
Each item in "participants" contains an array of namestring
Each item in "staff" contains a single namestring

See views/admin/setup/test/namestring
*/

const hint = '<span class="bg-primary-subtle">Full name (name1 name2), Date of birth (dd/mm/yy)</span>';

private $attributes = [];
private $_error = null ;

function __construct($namestring) {
	$namestring = filter_string($namestring);
	$buffer = preg_split("/ *[\t,] */", $namestring);
	
	//strip integers from array
	$input = [];
	foreach($buffer as $val) {
		if(!ctype_digit($val)) $input[] = $val;
	}
	# d($buffer, $input);
	
	// dob is last
	$dob = count($input)>1 ? array_pop($input) : '' ;
	$dt = self::sanitize_dob($dob);
	if($dt) $dob = $dt->format('d-M-Y');
	
	// name is the rest
	$name = implode(' ', $input);
			
	$this->attributes = [
		'name' => $name,
		'dob' => $dob,
	];
	
	$this->_error = $this->error($dt);
}

function __get($key) {
	return match($key) {
		'error' => $this->_error,
		default => $this->attributes[$key] ?? null
	};
}

function __debugInfo() {
	$arr =  $this->attributes;
	if($this->_error) $arr['error'] = $this->_error; 
	return $arr;
}

function __toString(): string {
	return implode(', ', $this->attributes);
}

private function error($dt) {
	self::calc();
	if(!$this->name) return "has no name";
	if(strlen($this->name)<6) return "has invalid name";
	if(!substr_count($this->name, ' ')) return "is not a full name"; 
	
	if(!$this->dob) return "has no DoB";
	if(!$dt) return "has invalid DoB";	
	if($dt>self::$calc['dob_max']) return "has invalid DoB";
	if($dt<self::$calc['dob_min']) return "has invalid DoB";
		
	// no error
	return null;
}

static function sanitize_dob($str) {
	self::calc();
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
			$retval = $format ? 
				\DateTime::createFromFormat($format, $str) : 
				new \DateTime($str) ;
			if($retval) break;	
			
		}
		// strip time from datetime
		$retval->setTime(0,0);
		// check for 2 digit year	
		$yr = (int) $retval->format('Y');
		if($yr<100) {
			// 2 digit year, bring it to this century
			$add = $yr>self::$calc['now_yr'] ? 1900 : 2000 ;
			$retval->add(new \DateInterval("P{$add}Y"));
		}
	}
	catch(\exception $ex) {
		# d($ex);
		$retval = null;
	}
	return $retval;
}

static $calc = null;
static function calc() {
	if(self::$calc) return;
	$calc = [];
	$now = new \DateTimeImmutable;
	$period = new \DateInterval("P5Y");
	$calc['dob_max'] = $now->sub($period);
	$period = new \DateInterval("P90Y");
	$calc['dob_min'] = $now->sub($period);
	$calc['now'] = $now;
	$calc['now_yr'] = (int) $now->format('y');
	
	self::$calc = $calc;
}

}
