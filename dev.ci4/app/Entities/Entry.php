<?php namespace App\Entities;

use CodeIgniter\Entity;

class Entry extends Entity {
	
protected $casts = [
	'user_id' => 'integer'
];

private $model;

function __construct() {
	$this->model = new \App\Models\Entries();
}

function breadcrumb($controller) {
	switch($controller) {
		case 'music': 
		case 'videos': 
			return [$this->url($controller), $this->num];
	}
	return '';
}

function url($controller) {
	switch($controller) {
		case 'music': 
		case 'videos': 
			return "{$controller}/edit/{$this->id}";
	}
	return '';
}

function role($controller, $method) {
	$event = $this->get_event();
	if(!$event) return 'none';
	$state = $event->$controller; // 0:closed, 1:edit, 2:view
	$check = "{$controller}-{$method}-{$state}";
	switch($check) {
		case 'music-edit-1':
		case 'music-view-1':
		case 'music-view-2':
		case 'videos-edit-1':
		case 'videos-view-1':
			$user_id = intval(session('user_id'));
			return $user_id===$this->user_id ? 'club' : 'admin' ;
		case 'videos-view-2':
			return '-';
		case 'music-edit-2':			
		case 'videos-edit-2':
		default:
			return 'admin';
	}
}

function perm($controller, $method) {
	$role = $this->role($controller, $method);
	return \App\Libraries\Auth::check_role($role);
}

private $_category = null;
public function get_category() {
	if($this->_category===null) {
		$mdl_entries = new \App\Models\Entries();
		$this->_category = $mdl_entries->entrycats->find($this->category_id);
	}
	return $this->_category;	
}

private $_event = null;
public function get_event() {
	if(!$this->_event) {
		$db = \Config\Database::connect();
		$sql = "SELECT `events`.id FROM `evt_entries` 
		INNER JOIN `evt_categories` ON `evt_entries`.`category_id` = `evt_categories`.`id`
		INNER JOIN `evt_disciplines` ON `evt_categories`.`discipline_id`=`evt_disciplines`.`id`
		INNER JOIN `events` ON `evt_disciplines`.`event_id`=`events`.`id`
		WHERE `evt_entries`.`id`={$this->id}";
		$query = $db->query($sql);
		$result = $query->getResult();
		if($result) {
			$model = new \App\Models\Events;
			$this->_event = $model->find($result[0]->id);
		}
	}
	return $this->_event;
}

public function getVideos() {
	$db_val = json_decode($this->attributes['videos'], 1);
	if(!$db_val) $db_val = [];
	$entity_val = [];
	$cat = $this->get_category();
	foreach($cat->videos as $exe) {
		$entity_val[$exe] = isset($db_val[$exe]) ? $db_val[$exe] : '' ;
	}
	return $entity_val;
}

public function setVideos($entity_val) {
	$db_val = json_encode($entity_val);
	$this->attributes['videos'] = $db_val;
	return $db_val;
}  

public function updateVideos() {
	$data = ['videos' => $this->attributes['videos']];
	$this->model->update($this->id, $data);
}

public function getMusic() {
	$db_val = json_decode($this->attributes['music'], 1);
	if(!$db_val) $db_val = [];
	$entity_val = [];
	$cat = $this->get_category();
	foreach($cat->music as $exe) {
		$entity_val[$exe] = isset($db_val[$exe]) ? $db_val[$exe] : 0 ;
	}
	return $entity_val;
}

public function setMusic($entity_val) {
	$db_val = json_encode($entity_val);
	$this->attributes['music'] = $db_val;
	return $db_val;
}

public function updateMusic() {
	$data = ['music' => $this->attributes['music']];
	$this->model->update($this->id, $data);
}

public function getRunorder() {
	$db_val = json_decode($this->attributes['runorder'], 1);
	$entity_val = [
		'rnd' => (int) $db_val['rnd'] ?? 0,
		'rot' => (int) $db_val['rot'] ?? 0
	];
	return $entity_val;
}

private $_rundata = null;
public function get_rundata($datakey=null) {
	if($this->_rundata===null) {
		$scoreboard = new \App\ThirdParty\scoreboard;
		$runorder = $this->runorder;		
		$cat = $this->get_category();
						
		$exe = [];
		$exeset_id = $cat->exercises ?? 0;
		foreach($scoreboard->get_exesets() as $sb_exeset) {
			if($sb_exeset['SetId']==$exeset_id) {
				$sb_exes = $sb_exeset['children'];
				$sb_sort = array_column($sb_exes, 'Order');
				array_multisort($sb_sort, $sb_exes);
				$key = $runorder['rot'] - 1;
				if(isset($sb_exes[$key])) $exe = $sb_exes[$key];
			}
		}
		
		// order is by running order, exercise order, exercise id
		// this makes a unique key for each rotation / exercise
		$arr = $runorder;
		$arr[] = $exe['Order'] ?? 999;
		$arr[] = $exe['ExerciseId'] ?? 999;
		$order = '';
		foreach($arr as $val) $order .= sprintf('%03d', $val);
				
		$export = $runorder;
		$export['exe'] = $exe['ShortName'] ?? '?' ;
				
		$this->_rundata = [
			'order' => $order,
			'exe' => $exe,
			'group' => implode('.', $runorder),
			'export' => $export
		];
	}
	return $this->_rundata[$datakey] ?? $this->_rundata;
}

public function setRunorder($entity_val) {
	$db_val = json_encode($entity_val);
	$this->attributes['runorder'] = $db_val;
	return $db_val;
}

}
