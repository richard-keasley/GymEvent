<?php namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Entry extends Entity {

protected $casts = [
	'user_id' => 'integer',
	'category_id' => 'integer',
	'guest' => 'integer',
	'name' => 'string'
];

private $model;

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
	$db_val = filter_json($this->attributes['videos'] ?? null);
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
	$model = new \App\Models\Entries;
	$model->update($this->id, $data);
}

public function getMusic() {
	$db_val = filter_json($this->attributes['music'] ?? null);
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
	$model = new \App\Models\Entries;
	$model->update($this->id, $data);
}

public function getRunorder() {
	$db_val = filter_json($this->attributes['runorder'] ?? null);
	$entity_val = [
		'rnd' => intval($db_val['rnd'] ?? 0),
		'rot' => intval($db_val['rot'] ?? 1), // normally start on first rotation
		'exe' => intval($db_val['exe'] ?? 0)
	];
	return $entity_val;
}

private $_rundata = null;
public function get_rundata($datakey=null) {
/* From Kev

I need which exercise entries start on and assume they enter the competition in the first rotation of their round. Its a bit more complex if the competition is big enough that some entries start their competition in a different rotation. In which case I would need to know which exercise and which rotation they begin. 

read help / entries / edit

NB: teamgym export calculates rundata according to progtable
look in controllers/admin/entries->export()
*/
	
	
	if($this->_rundata===null) {
		$scoreboard = new \App\ThirdParty\scoreboard;
		$rundata = $this->runorder;		
		$cat = $this->get_category();
						
		$exe = [];
		$exeset_id = $cat->exercises ?? 0;
		foreach($scoreboard->get_exesets() as $sb_exeset) {
			if($sb_exeset['SetId']==$exeset_id) {
				$sb_exes = $sb_exeset['children'];
				$key = $rundata['exe'] - 1;
				if(isset($sb_exes[$key])) $exe = $sb_exes[$key];
			}
		}
		
		// this is the working group
		$arr = $rundata; // [round, start rotation, start exercise]
		// in case there is more than one apparatus in the group
		$arr[] = $exe['Order'] ?? 999;
		// in case 2 disciplines have same apparatus order
		$arr[] = $exe['ExerciseId'] ?? 999;
		$order = '';
		foreach($arr as $val) $order .= sprintf('%03d', $val);
		
		// scoreboard export
		$export = $rundata;
		$export['exe'] = $exe['ShortName'] ?? '?' ;
		
		// friendly version of scoreboard
		$runorder = [
			'round' => "Round {$rundata['rnd']}/{$rundata['rot']}",
			'exercise' => $exe['Name'] ?? '?' 
		];
								
		$this->_rundata = [
			'order' => $order,
			'exe' => $exe,
			'group' => implode('.', $rundata),
			'export' => $export,
			'runorder' => $runorder
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
