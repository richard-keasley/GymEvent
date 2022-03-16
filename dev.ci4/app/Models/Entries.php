<?php namespace App\Models;
use CodeIgniter\Model;

class Entries extends Model {

protected $table      = 'evt_entries';
protected $primaryKey = 'id';
protected $allowedFields = ['num', 'category_id', 'name', 'dob', 'music', 'videos', 'user_id'];
protected $returnType   = 'App\Entities\Entry';

protected $disciplines = null;
protected $entrycats = null;

function __construct() {
    parent::__construct();
    $this->disciplines = $this->builder('evt_disciplines');
    $this->entrycats = new \App\Models\EntryCats;
}

public function evt_discats($event_id, $entries=1) {
	$return = [];
	foreach($this->evt_disciplines($event_id) as $dis) {
		$cats = $this->entrycats
			->where('discipline_id', $dis->id)
			->orderBy('sort', 'ASC')
			->findAll();
		foreach($cats as $cat_key=>$entrycat) {
			$entrycat->entries = $entries ? $this->cat_entries($entrycat->id) : [] ;
			$dis->cats[$cat_key] = $entrycat;
		}
		$return[] = $dis;
	}
	return $return;
}

function evt_users($event_id) {
	// list all users who have entries in event event_id
	$sql = "SELECT DISTINCT `users`.* 
	FROM `users` 
	INNER JOIN `evt_entries` ON `users`.`id`=`evt_entries`.`user_id` 
	INNER JOIN `evt_categories` ON `evt_entries`.`category_id`= `evt_categories`.`id` 
	INNER JOIN `evt_disciplines` ON `evt_categories`.`discipline_id`=`evt_disciplines`.`id` 
	INNER JOIN `events` ON `evt_disciplines`.`event_id`=`events`.`id` 
	WHERE `events`.`id`={$event_id}
	ORDER BY `users`.`name`";
	$query = $this->query($sql);
	
	$retval = [];
	foreach($query->getResultArray() as $row) {
		$retval[$row['id']] = new \App\Entities\User($row);
	}
	return $retval;
} 

// disciplines 

public function evt_disciplines($event_id) {
	$query = $this->disciplines->orderBy('name', 'ASC')->getWhere(['event_id'=>$event_id]);
	return $query->getResult();
} 

public function update_discipline($id, $data) {
	$this->disciplines->where('id', $id);
	return $this->disciplines->update($data);
}

/* entries */
public function renumber($event_id) {
	$bld_ent = $this->db->table('evt_entries');
	$num = 1;
	
	$qry_dis = $this->disciplines->orderBy('name')->getWhere(['event_id'=>$event_id]);
	foreach($qry_dis->getResult() as $dis) {
		$entrycats = $this->entrycats
			->where('discipline_id', $dis->id)
			->orderBy('sort', 'ASC')
			->findAll();
		foreach($entrycats as $entrycat) {
			$entries = $this->where('category_id', $entrycat->id)->findAll();
			foreach($entries as $entry) {
				$entry->num = $num;
				$this->save($entry);
				$num++;
			}
			$num = $num + 4;
		}
	}
}

public function cat_entries($category_id) {
	return $this->where('category_id', $category_id)->orderBy('num', 'ASC')->findAll();
}

/* delete */
public function delete_user($user_id) {
	$this->where('user_id', $user_id)->delete();
}

public function delete_event($event_id) {
	$qry = $this->disciplines->getWhere(['event_id'=>$event_id]);
	foreach($qry->getResult() as $dis) {
		$this->delete_discipline($dis->id);
	}
}

public function delete_discipline($discipline_id) {
	$entrycats = $this->entrycats
		->where('discipline_id', $discipline_id)
		->findAll();
	foreach($entrycats as $entrycat) $this->delete_category($entrycat->id);
	$this->disciplines->delete(['id'=>$discipline_id]);
}

public function delete_category($category_id) {
	$this->where('category_id', $category_id)->delete();
	$this->entrycats->delete($category_id);
}



public function populate($event_id) {
	// check event 
	$mdl_events = new \App\Models\Events;
	$event = $mdl_events->find($event_id);
	if(!$event) return false;
	if($event->clubrets!=2) return false;
	// read participants from club returns
	$participants = $event->participants();
	if(!$participants) return false;
	
	$this->delete_event($event_id);
	$count = 0;
	// copy participants from club returns to entries
	foreach($participants as $dis) { 
		$dis_id = $this->add_discipline(['event_id'=>$event_id, 'name'=>$dis['name']]);
		foreach($dis['cats'] as $sort=>$cat) {
			$cat_arr = [
				'discipline_id' => $dis_id, 
				'name' => $cat['name'], 
				'sort' => $sort
			];
			$cat_id = $this->entrycats->insert($cat_arr);
			foreach($cat['entries'] as $entkey=>$entry) {
				$entry['category_id'] = $cat_id;
				unset($entry['club']);
				$entry['dob'] = date('Y-m-d', $entry['dob']);
				$this->add_entry($entry);
				$count++;
			}
		}
	}
	$this->renumber($event_id);
	return $count;
}

public function add_discipline($data) {
	if(empty($data['event_id'])) return 0;
	if(empty($data['name'])) return 0;
	if(empty($data['abbr'])) $data['abbr'] = $data['name'];
	$this->disciplines->insert($data);
	return $this->db->insertID();
}

public function add_entry($data) {
	if(empty($data['category_id'])) return 0;
	if(empty($data['name'])) return 0;
	$this->insert($data);
	return $this->db->insertID();
}

} 

class EntryCats extends Model {

protected $table      = 'evt_categories';
protected $primaryKey = 'id';
protected $allowedFields = ['discipline_id', 'name', 'abbr', 'sort', 'exercises', 'music', 'videos'];
protected $returnType   = 'App\Entities\EntryCat';

protected $validationRules = [
	'discipline_id' => 'integer|greater_than[0]',
	'name' => 'required|min_length[2]'
];
protected $beforeInsert = ['beforeInsert'];

public function beforeInsert($arr) {
	if(empty($arr['data']['abbr'])) {
		$arr['data']['abbr'] = $arr['data']['name'];
	}
	return $arr;
}

}
