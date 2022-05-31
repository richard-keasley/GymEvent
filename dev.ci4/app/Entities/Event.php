<?php namespace App\Entities;

use CodeIgniter\Entity;

class Event extends Entity {
	
protected $casts = [
	'staffcats' => 'csv'
];

public function getDiscats() {
	$db_val = json_decode($this->attributes['discats'], 1);
	if(!$db_val) return [];
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
	$db_val = json_decode($this->attributes['player'], 1);
	if(!$db_val) $db_val = [];
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
public function file_link($basename) {
	return \App\Libraries\View::download("/public/events/{$this->id}/files/{$basename}");
}

public function file_path($basename='') {
	return FCPATH . "public/events/{$this->id}/files/{$basename}";
}

public function getFiles() {
	$files = [];
	foreach(glob($this->file_path('*')) as $file) {
		$file = basename($file);
		if(strpos($file, 'index.')!==0) $files[] = $file;
	}
	return $files;
}

public function file($basename) {
	$file = new \CodeIgniter\Files\File($this->file_path($basename));
	if(!$file->getRealPath()) throw new \RuntimeException("Can't find file $basename", 404);
	return $file;	
}

private $_clubrets = null;
public function clubrets() {
	if(is_null($this->_clubrets)) {
		$this->_clubrets = [];
		$model = new \App\Models\Clubrets;
		// include unlisted users 
		$sql = "SELECT `clubrets`.`id` FROM `clubrets` 
			INNER JOIN `users` ON `clubrets`.`user_id`=`users`.`id`
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
				$namestring = new \App\Entities\namestring($name);
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
			$subcat = isset($subcats[$dis_name]) ? $subcats[$dis_name] : '';
			if($subcat) {
				$cat_name .= ' ' . date($subcat, $entry['dob']);
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
				$namestring = new \App\Entities\namestring($row['name']);
				$this->_staff[] = [
					'club' => $club,
					'user_id' => $clubret->user_id,
					'cat' => $row['cat'],
					'name' => $namestring->name,
					'dob' => $namestring->dob,
					'bg' => $namestring->bg
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
					return anchor(base_url("clubrets/add/{$this->id}"), 'enter this event', ['class'=>'btn btn-outline-primary']);
				}
				break;
				
			case self::states['view']:
				return getlink("entries/view/{$this->id}", 'entries');
				break;
		}
		break;

		case 'videos':
		switch($this->videos) {
			case self::states['edit']:
				$href = base_url("videos/view/{$this->id}");
				$label = "videos";
				if($user_id) {
					$attr = [
						'class' => 'nav-link', 
						'title' => "Alter your videos"
					];
				}
				else {
					$attr = [
						'class' => 'btn btn-outline-secondary', 
						'title' => "login to alter videos"
					];
				}
				return anchor($href, $label, $attr);
			
			case self::states['view']:
				return getlink("videos/view/{$this->id}", 'videos');
			
			default:
			return '';
		}
		break;

		case 'music':
		switch($this->music) {
			case self::states['edit']:
			case self::states['view']:
				$href = base_url("music/view/{$this->id}");
				$label = "music";
				if($user_id) {
					$attr = [
						'class' => 'nav-link', 
						'title' => "View your music"
					];
				}
				else {
					$attr = [
						'class' => 'btn btn-outline-secondary', 
						'title' => "login to view music"
					];
				}
				return anchor($href, $label, $attr);
							
			case self::states['waiting']:
			case self::states['finished']:
			default:
			return '';
		}
		break;
		
		case 'player':
		$path = "control/player/view/{$this->id}";
		switch($this->music) {
			case self::states['waiting']:
				$perm = 0; break;
			case self::states['view']:
				$perm = 1; break;
			case self::states['edit']:
			case self::states['finished']:
			default:
			$perm = \App\Libraries\Auth::check_path($path);	
		}
		if($perm) { 
			$attr = [
				'class' => 'nav-link', 
				'title' => "View music player"
			];
			return anchor(base_url($path), 'player', $attr);            
		}
		break;
		
		case 'admin':
		return getlink("admin/events/view/{$this->id}", 'admin');	
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
