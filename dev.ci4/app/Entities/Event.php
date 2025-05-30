<?php namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Event extends Entity {

protected $casts = [
	'staffcats' => 'csv',
	'clubrets' => 'integer',
	'music' => 'integer',
	'private' => 'integer',
	'stafffee' => 'float',
];

const icons = [
	'future' => '<i class="bi bi-calendar-x text-danger" title="Not yet open"></i>',
	'past' => '<i class="bi bi-calendar-check text-success" title="Finished"></i>',
	'current' => '<i class="bi bi-calendar-fill text-success" title="Current"></i>',
	'hidden' => '<i class="bi bi-x-circle text-danger" title="Not listed"></i>',
	'private' => '<i class="bi bi-file-lock2 text-info" title="Private"></i>'
];

public function getDates() {
	$db_val = filter_json($this->attributes['dates'] ?? null);
	$entity_val = [];
	$keys = ['clubrets_opens', 'clubrets_closes', 'music_opens', 'music_closes'];
	foreach($keys as $key) {
		$entity_val[$key] = null ;
		try {
			$date = $db_val[$key] ?? null;
			if($date) {
				$date = new \datetime($date);
				$entity_val[$key] = $date->format('Y-m-d');
			}
		}
		catch(\throwable $ex) {
			// do nothing, value was set at start of loop
		}
	}
	return $entity_val;
}

public function setDates($entity_val) {
	# d($entity_val);
	/*
	foreach($entity_val as $key=>$val) {
		$entity_val[$key] = $val->format('Y-m-d');
	}
	*/
	$db_val = json_encode($entity_val);
	$this->attributes['dates'] = $db_val;
	return $db_val;
}

public function getDiscats() {
	$db_val = filter_json($this->attributes['discats'] ?? null);
	$entity_val = [];
	foreach($db_val as $db_row) {
		if(empty($db_row['name'])) $db_row['name'] = '';
		if(empty($db_row['inf']) || !is_array($db_row['inf'])) $db_row['inf'] = [];
		if(empty($db_row['cats']) || !is_array($db_row['cats'])) $db_row['cats'] = [];
		if(empty($db_row['opts']) || !is_array($db_row['opts'])) $db_row['opts'] = [];
		$entity_val[] = $db_row;
	}
	return $entity_val;
} 

public function setDiscats($entity_val) {
	$db_val = json_encode($entity_val);
	$this->attributes['discats'] = $db_val;
	return $db_val;
}

public function getPlayer() {
	$db_val = filter_json($this->attributes['player'] ?? null);

	$entity_val = [];
	$entity_row = [];
	foreach($db_val as $db_row) {
		foreach(self::player_row as $key=>$val) {
			$entity_row[$key] = isset($db_row[$key]) ? $db_row[$key] : $val ;	
		}
		$entity_val[] = $entity_row;
	}
	return $entity_val;
}

public function setPlayer($entity_val) {
	$db_val = json_encode($entity_val);
	$this->attributes['player'] = $db_val;
	return $db_val;
}

public function breadcrumb($method='', $folder='') {
	if(!$method) $method = 'view';
	if($folder) $folder .= '/';
	$label = $method=='view' ? $this->title : $method ;
	return ["{$folder}events/{$method}/{$this->id}", $label];
}

public function viewstate() {
	$current = [1, 2]; 
	if(in_array($this->clubrets, $current)) {
		// always current
		$viewstate = 'current';
	}
	else {
		// determine if past or future
		$now = date("Y-m-d");
		if($this->date < $now) $viewstate = 'past';
		elseif($this->date > $now) $viewstate = 'future';
		else $viewstate = 'current'; // today
	}
	return $viewstate;
}

public function discat_inf($dis_name, $inf_name) {
	$ret = '';
	foreach($this->discats as $dis) {
		if($dis['name']==$dis_name) {
			if(isset($dis['inf'][$inf_name])) $ret = $dis['inf'][$inf_name];
		}
	}
	switch($inf_name) {
		case 'team':
			return $ret ? 1 : 0 ;
		case 'fe':
		case 'fg': 
			return floatval($ret);
		case 'n': 
			return intval($ret);
	}
	return $ret;
}

const player_row = [
	'exe' => '', 
	'title' => '', 
	'description' => '', 
	'entry_nums' => [] 
];

/* event uploads */
public function filepath($sub='') {
	if($sub) $sub .= '/';
	return FCPATH . "public/events/{$this->id}/{$sub}";
}

public function getDownloads() {
	// downloads for this event
	$files = new \CodeIgniter\Files\FileCollection();
	$filepath = $this->filepath('files');
	if(is_dir($filepath)) {
		$files->addDirectory($filepath);
		$files->removePattern('index.*');
	}
	return $files;
}

public function getFiles() {
	// all files associated with this event
	$filepath = $this->filepath();
	$files = new \CodeIgniter\Files\FileCollection();
	if(is_dir($filepath)) {
		$files->addDirectory($filepath, true);
	}
	return $files;
}

private $_clubrets = null;
public function clubrets() {
	if(is_null($this->_clubrets)) {
		$this->_clubrets = [];
		$model = new \App\Models\Clubrets;
		// include unlisted users 
		$sql = "SELECT `clubrets`.`id`, `users`.`name` FROM `clubrets` 
			LEFT JOIN `users` ON `clubrets`.`user_id`=`users`.`id`
			WHERE `clubrets`.`event_id`='{$this->id}'
			ORDER BY `users`.`name`;";
		$res = $model->query($sql)->getResultArray();
		
		if($res) {
			$ids = array_column($res, 'id');
			$clubrets = $model->find($ids);
			// sort clubrets into same order as $res (above)
			foreach($clubrets as $clubret) {
				$key = array_search($clubret->id, $ids);
				$this->_clubrets[$key] = $clubret;
			}
			ksort($this->_clubrets);
		}
	}
	return $this->_clubrets;
}

function users($source='entries', $entity=true) {

switch($source) {
	case 'clubrets':
	$sql = "SELECT DISTINCT `users`.* 
	FROM `users` 
	INNER JOIN `clubrets` ON `users`.`id`=`clubrets`.`user_id`
	INNER JOIN `events` ON `clubrets`.`event_id` = `events`.`id`
	WHERE `events`.`id`={$this->id}
	ORDER BY `users`.`name`";
	break;
	
	default:
	// list all users who have entries in this event
	$sql = "SELECT DISTINCT `users`.* 
	FROM `users` 
	INNER JOIN `evt_entries` ON `users`.`id`=`evt_entries`.`user_id` 
	INNER JOIN `evt_categories` ON `evt_entries`.`category_id`= `evt_categories`.`id` 
	INNER JOIN `evt_disciplines` ON `evt_categories`.`discipline_id`=`evt_disciplines`.`id` 
	INNER JOIN `events` ON `evt_disciplines`.`event_id`=`events`.`id` 
	WHERE `events`.`id`={$this->id}
	ORDER BY `users`.`name`";
}
	
	$model = new \App\Models\Users;
	$query = $model->query($sql);
	$res = $query->getResultArray();
	
	$retval = [];
	foreach($res as $row) {
		$retval[$row['id']] = $entity ?
			new \App\Entities\User($row) : 
			$row['name'];
	}
	return $retval;
} 

public function entries() {
	$model = new \App\Models\Entries();
	return $model->evt_discats($this->id);
}

public function participants() {
	// get a sorted list of participants from club returns 
	$mdl_users = new \App\Models\Users();
	$participants = [];
	$dis_names = []; $cat_names = [];

	// generate category sorting and sub category names
	$sort = []; $subcats = []; $teams = [];
	foreach($this->discats as $dis) {
		$dis_name = $dis['name'];
		$sort[$dis_name] = $dis['cats'];
		$subcats[$dis_name] =$this->discat_inf($dis['name'], 'cat');
		$teams[$dis_name] = $this->discat_inf($dis['name'], 'team');
	}
	# d($subcats);

	foreach($this->clubrets() as $clubret) {
		$user = $mdl_users->withDeleted()->find($clubret->user_id);
		$club = $user ? $user->abbr : '';
			
		foreach($clubret->participants as $row) {
			// get discipline
			$dis_name = $row['dis'];
			if(!$dis_name) continue; // empty 
			$dis_key = array_search($dis_name, $dis_names);
			if($dis_key===false) { // create new discipline
				$dis_key = count($dis_names);
				$dis_names[$dis_key] = $dis_name;
				$cat_names[$dis_key] = [];
				$participants[$dis_key] = [
					'name' => $dis_name,
					'cats' => []
				];
			}
		
			// build entry
			$arr = [];
			foreach($row['names'] as $name) {
				$namestring = new \App\Libraries\Namestring($name);
				$arr['name'][] = $namestring->name;
				$arr['dob'][] = $namestring->dob;				
			}
						
			$name = !empty($teams[$dis_name]) && $row['team'] ? $row['team'] : implode(", ", $arr['name']);
			$entry = [
				'name' => $name,
				'dob' => min($arr['dob']),
				'user_id' => $clubret->user_id,
				'club' => $club,
				'opt' => $row['opt']
			];	
			
			// get category
			$cat_name = implode(' ', $row['cat']);
			$subcat = $subcats[$dis_name] ?? false ;
			if($subcat) {
				$cat_name .= ' ';
				$dt = \App\Libraries\Namestring::get_dt($entry['dob']);
				$cat_name .= $dt ? $dt->format($subcat) : '??' ;
			}

			$cat_key = array_search($cat_name, $cat_names[$dis_key]);
			if($cat_key===false) {
				$cat_key = count($cat_names[$dis_key]);
				$cat_names[$dis_key][$cat_key] = $cat_name;
				$participants[$dis_key]['cats'][$cat_key] = [
					'name' => $cat_name,
					'entries' => []
				];
			}
			$participants[$dis_key]['cats'][$cat_key]['entries'][] = $entry;
		}
	}

	// sort participants
	$dis_sort = [];
	foreach($participants as $diskey=>$dis) {
		$dis_sort[] = $dis['name'];
		$cat_sorts = isset($sort[$dis['name']]) ? $sort[$dis['name']] : [];
		$cat_sort = [];
		foreach($dis['cats'] as $cat) {
			$cats = explode(' ', $cat['name']);
			$cats = array_pad($cats, count($cat_sorts), '');
			$this_sort = [];
			foreach($cat_sorts as $key=>$cat_row) {
				$pos = array_search($cats[$key], $cat_row);
				if($pos===false) $pos = 99;
				$this_sort[] = sprintf("%03d", $pos); 
			}
			$sort_order = implode('-', $this_sort);
			# echo "{$sort_order}: {$cat['name']}<br>";
			$cat_sort[] = $sort_order;
		}
		
		array_multisort($cat_sort, $dis['cats']);
		$participants[$diskey]['cats'] = $dis['cats'];
	}
	array_multisort($dis_sort, $participants);
	
	return $participants;
}

private $_staff = null;
public function staff() {
	if(is_null($this->_staff)) {
		$this->_staff = [];
		$mdl_users = new \App\Models\Users();
		foreach($this->clubrets() as $clubret) {
			$user = $mdl_users->withDeleted()->find($clubret->user_id);
			$club = $user ? $user->abbr : '';
			foreach($clubret->staff as $row) {
				$namestring = new \App\Libraries\Namestring($row['name']);
				$this->_staff[] = [
					'club' => $club,
					'user_id' => $clubret->user_id,
					'cat' => $row['cat'],
					'name' => $namestring->name,
					'dob' => $namestring->dob,
					# 'bg' => $namestring->bg
				];
			}
		}
	}
	return $this->_staff;
}

public function link($type, $user_id=0) {
	if($this->deleted_at) return '';
	if(!$user_id) $user_id = intval(session('user_id'));
	switch($type) {
		case 'clubrets': 
		if(!\App\Entities\Clubret::enabled()) return '';
		case 'entries':
		switch($this->clubrets) {
			case self::states['edit']:
				if($user_id) {
					$clubrets = new \App\Models\Clubrets();
					$clubret = $clubrets->lookup($this->id, $user_id);
					if($clubret) { // update
						return getlink($clubret->url('view'), 'view return');
					}
					else { // add
						return getlink("clubrets/add/{$this->id}/{$user_id}", 'enter this event');
					}
				}
				else { // no user
					return anchor("clubrets/add/{$this->id}", 'enter this event', ['class'=>'btn btn-outline-primary']);
				}
				break;
				
			case self::states['view']:
				return getlink("entries/view/{$this->id}", 'entries');
				break;
		}
		break;

		case 'music':
		if(!\App\Libraries\Track::enabled()) return '';
		// can't check perm because may not be logged in
		$href = "music/view/{$this->id}";
		$attrs = $user_id ?
			[
				'class' => 'nav-link', 
				'title' => "View your music"
			] : 
			[
				'class' => 'btn btn-outline-secondary', 
				'title' => "login to view music"
			] ;
		return match($this->music) {
			self::states['waiting'] => '',
			self::states['edit'] => anchor($href, 'music', $attrs),
			self::states['view'] => '',
			self::states['finished'] => '',
			default => ''
		};
		break;
		
		case 'player':
		$appvars = new \App\Models\Appvars();
		$teamtime  = $appvars->get_value('teamtime.settings');
		$tt_event = $teamtime['event_id'] ?? 0;
		$path = $tt_event==$this->id ?
			'control/teamtime/player' : 
			"control/player/view/{$this->id}";

		$perm = match($this->music) {
			self::states['waiting'] => 0,
			self::states['edit'] => \App\Libraries\Auth::check_path($path),
			self::states['view'] => 1,
			self::states['finished'] => 0,
			default => 0
		};
		if($perm) { 
			$attrs = [
				'class' => 'nav-link', 
				'title' => "View music player"
			];
			return anchor($path, 'player', $attrs);            
		}
		break;
		
		case 'admin':
		$link = getlink("admin/events/view/{$this->id}", 'admin');
		if(!$link) $link = getlink("control/events/view/{$this->id}", 'admin');
		return $link;

		case 'teamtime':
		if(in_array($this->clubrets, [1, 2])) {
			$event_id = \App\Libraries\Teamtime::get_value('settings', 'event_id');
			if($event_id==$this->id) {
				$link = getlink("control/teamtime");
				return $link ? $link : getlink("teamtime");
			}
		}
		break;
	}
	return '' ;
}

public function user_entries($user_id) {
	$db = \Config\Database::connect();
	$sql = "SELECT `evt_entries`.*
	FROM `evt_disciplines` 
	INNER JOIN `evt_categories` ON `evt_disciplines`.`id`=`evt_categories`.`discipline_id`
	INNER JOIN `evt_entries` ON `evt_categories`.`id`=`evt_entries`.`category_id`
	WHERE `evt_disciplines`.`event_id`={$this->id}";
	$query = $db->query($sql);
	return $query->getResult();
}

public function placeholders() {
	$retval = [];
	foreach($this->toArray() as $key=>$val) {
		switch($key) {
			// ignore these 
			case 'description':
			case 'payment':
			case 'participants':
			case 'staff':
			break;
			
			case 'dates':
			$retval[$key] = [];
			foreach($val as $subkey=>$subval) {
				$retval[$key][$subkey] = (new \datetime((string) $subval))->format('l j F');
			}
			break;
			
			case 'date':
			$dt = new \datetime($val);
			$retval[$key] = $dt->format('l j F');
			break;
			
			default:
			if(!is_array($val)) {
				$retval[$key] = $val;
			}
		}
	}
	$retval['url'] = site_url("events/view/{$this->id}");
	return $retval;
}

/* event states */
const states = [
	'waiting' => 0,
	'edit' => 1,
	'view' => 2,
	'finished' => 3
];
const state_colours = ['danger', 'warning', 'success', 'danger'];
static function state_label($key) {
	$label = array_search($key, self::states);
	return $label===false ? 'unknown' : $label;
}
static function state_colour($key) {
	return isset(self::state_colours[$key]) ? self::state_colours[$key] : 'light';
}

}  
