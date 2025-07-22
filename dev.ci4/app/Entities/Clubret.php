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
		$namestring = new \App\Libraries\Namestring($row['name']);
		$row['name'] = (string) $namestring;
		$staff[] = $row;
	}
	$this->attributes['staff'] = json_encode($staff);
	return $this;
}
	
private $_event = null;
public function event() {
		d(debug_backtrace());

	if(!$this->_event) {
		$model = new \App\Models\Events();
		$this->_event = $model->withDeleted()->find($this->event_id);
	}
	return $this->_event;
}

private $attrs = [];
public function getUser() {
	$key = 'user';
	if(!isset($this->attrs[$key])) {
		$this->attrs[$key] = model('Users')->withDeleted()->find($this->user_id);
	}
	return $this->attrs[$key];
}

public function getEvent() {
	$key = 'event';
	if(!isset($this->attrs[$key])) {
		$this->attrs[$key] = model('Events')->withDeleted()->find($this->event_id);
	}
	return $this->attrs[$key];
}

function breadcrumb($method='view', $controller='') {
	if($method=='view') {
		$user = $this->user;
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
function errors($err_type=null, $max=999) {
	$ret = '';
	$count = 0;
	foreach($this->errors as $type=>$errors) {
		if(!$err_type || $err_type===$type) {
			$text1 = $err_type ? '' : "{$type}: ";
			foreach($errors as $error) {
				if($count<$max) {
					$ret .= sprintf('<li class="list-group-item list-group-item-danger">%s%s</li>', $text1, $error);
					$count++;
				}
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
			$namestring = new \App\Libraries\Namestring($name);
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
	$this->errors = [];
	$errors = [];
	if($this->participants) { 
		foreach($this->participants as $rowkey=>$row) {
			$row_error = '';
			if(empty($row['dis'])) $row_error = "has invalid discipline";
			if(empty($row['cat'])) $row_error = "has invalid category";
			foreach($row['names'] as $name) {
				$namestring = new \App\Libraries\Namestring($name);
				$name_error = $namestring->error;
				if($name_error) $row_error = $name_error;
			}
			if($row_error) $errors[] = sprintf('row %u %s', $rowkey+1, $row_error);
		}
	}
	else {
		$errors[] = "No participants entered in this return";
	}
	
	if($this->event->terms && !$this->terms) {
		$errors[] = "Clubs entries will only be accepted after they agree to the terms of this event";
	}
	
	if($errors) $this->errors['participants'] = $errors;
	
	if(!empty($this->event->staffcats[0])) {
		$errors = [];
		if($this->staff) {
			foreach($this->staff as $rowkey=>$row) {
				$namestring = new \App\Libraries\Namestring($row['name']);
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

public function getFees() {
	$fees = []; $evt_fees = []; 
	foreach($this->participants as $participant) {
		$dis = $participant['dis'];
		if(empty($evt_fees[$dis])) {
			$evt_fees[$dis] = [
				'gymnast' => $this->event->discat_inf($dis, 'fg'),
				'entry' => $this->event->discat_inf($dis, 'fe')
			];
		}
		$count = count($participant['names']);
		$fee = $count * $evt_fees[$dis]['gymnast'] + $evt_fees[$dis]['entry'];
		if(!isset($fees[$dis])) $fees[$dis] = [$dis, 0];
		$fees[$dis][1] += $fee;
	}
	
	if($this->event->stafffee && !$this->stafffee) {
		$fees['_stafffee'] = ['Staff', $this->event->stafffee];
	}
		
	return $fees;
}

}
