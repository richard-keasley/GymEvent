<?php namespace App\Entities;

use CodeIgniter\Entity;

class Clubret extends Entity {

static function enabled() {
	return \App\Libraries\Auth::check_path('clubrets', 0) != 'disabled';
}

public function getStaff() {
	#d($this); die;
	$db_val = $this->attributes['staff'] ?? '[]';
	$db_val = json_decode($db_val, 1) ?? [];
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
		if($namestring->name) {
			$row['name'] = $namestring->csv;
			$staff[] = $row;
		}
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
	if(!$this->_user) {
		$model = new \App\Models\Users();
		$this->_user = $model->withDeleted()->find($this->user_id);
	}
	return $this->_user;
}

function breadcrumb($method='view', $controller='') {
	if($method=='view') {
		$user = $this->user();
		$label = $user->name;
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
	$db_val = $this->attributes['participants'] ?? '[]';
	$db_val = json_decode($db_val, 1) ?? [];
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
			if($namestring->name) $row_names[] = $namestring->csv;
		}
		if($row_names) {
			$row['names'] = implode("\n", $row_names);
			$row['cat'] = implode(',',$row['cat']);
			$participants[] = $row;
		}	
	}
	$this->attributes['participants'] = json_encode($participants);
	return $this;
}

public function check() {
	$this->errors = [];
	$errors = [];
	if($this->participants) { 
		foreach($this->participants as $rowkey=>$row) {
			$row_error = '';
			if(empty($row['dis'])) $row_error = "has invalid discipline";
			if(empty($row['cat'])) $row_error = "has invalid category";
			foreach($row['names'] as $name) {
				$namestring = new \App\Entities\namestring($name);
				$name_error = $namestring->error();
				if($name_error) $row_error = $name_error;
			}
			if($row_error) $errors[] = sprintf('row %u %s', $rowkey+1, $row_error);
		}
	}
	else {
		$errors[] = "No participants entered in this return";
	}
	if($errors) $this->errors['participants'] = $errors;
	
	$event = $this->event();
	if(!empty($event->staffcats[0])) {
		$errors = [];
		if($this->staff) {
			foreach($this->staff as $rowkey=>$row) {
				$namestring = new \App\Entities\namestring($row['name']);
				$error = $namestring->error();
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
	if($op=='fees') return $fees;
	
	$total = array_sum(array_column($fees, 1));
	
	if($op=='htm') {
		$vartable = new \App\Views\Htm\Vartable;
		foreach($fees as $fee) {
			$vartable->items[$fee[0]] = [$fee[1], 'money'];
		}
		$vartable->footer = [$total, 'money'];
		return $vartable->htm();
	}
	return $total;
}

}

class namestring {
/* 
a namestring is a comma separated string containing:
name 1, name 2, BG number, DoB
Only used in entity "clubret"
Each item in "participants" contains an array of namestring
Each item in "staff" contains a single namestring
*/ 

public $namestring = '';
public $name = '';
public $bg = '';
public $dob = null; // unix timestamp
public $csv = '';

function __construct($namestring) {
	$this->namestring = $namestring;
	$arr = preg_split("/ *[\t,] *+/", $namestring);
	$arr = array_pad($arr, 4, '');
	$arr = array_slice($arr, 0, 4);
	
	$this->name = trim($arr[0] . ' ' . $arr[1]);
	$this->bg = $arr[2];
	
	if($arr[3]) $this->dob = self::euro_time($arr[3]);
	if($this->dob) $arr[3] = $this->htm_dob();
	$this->csv = $namestring ? implode(', ', $arr) : '' ;
}

function htm_dob() {
	$timestamp = $this->dob;
	return is_null($timestamp) ? '' : date('d-M-Y', $timestamp) ;
}

static function euro_time($str) {
	// ensure form input is formatted
	$ret = trim($str);				
	$ret = preg_replace('!\s+!', ' ', $ret); // remove multiple spaces
	$ret = str_replace(array('/','.',' '), '-', $ret); // replace separator
	$ret = array_pad( explode('-',$ret,3), 3, ''); // 3 items
	if(ctype_digit($ret[0]) && ctype_digit($ret[1])) $ret = array_reverse($ret); // don't reverse non-numerical month
	$ret = implode('-', $ret); // convert to Y-m-d
	return strtotime($ret);
}

function error() {
	if(!$this->namestring) return "is empty";
	if(!$this->name) return "has not been completed";
	
	if(strlen($this->name)<6) return "has invalid name";
	
	if(empty($this->bg)) return "has no BG number";
	if(!preg_match('/^[0-9]*$/', $this->bg)) return "has invalid BG number";
	
	if(empty($this->dob)) return  "has invalid DoB";
	else {
		$yr0 = date('Y') - 90;
		$yr1 = date('Y') - 3;
		$yr = date('Y', $this->dob);
		if($yr<$yr0 || $yr>$yr1) return "has invalid YoB";
	}
	
	return '';
}

}
