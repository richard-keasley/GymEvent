<?php namespace App\Controllers\Admin;

class Entries extends \App\Controllers\BaseController {
	
private $model = null;

function __construct() {
	$this->data['breadcrumbs'][] = 'admin';
	$this->data['breadcrumbs'][] = 'admin/events';
	$this->mdl_entries = new \App\Models\Entries();
	$this->data['title'] = "entries";
	$this->data['heading'] = "Event entries - admin";
}
	
private function find($event_id) {
	$evt_model = new \App\Models\Events();
	$this->data['event'] = $evt_model->find($event_id);
	if(!$this->data['event']) throw new \RuntimeException("Can't find event $event_id", 404);
	$this->data['entries'] = $this->mdl_entries->evt_discats($event_id);
	$this->data['title'] = $this->data['event']->title;
	$this->data['heading'] = $this->data['event']->title;
}
	
public function index() {
	$mdl_events = new \App\Models\Events();
	$events = [];
	$sql = "SELECT DISTINCT `events`.`id` FROM `events` 
	INNER JOIN `evt_disciplines` ON `evt_disciplines`.`event_id`=`events`.`id`
	WHERE `events`.`clubrets`=2
	ORDER BY `events`.`date` DESC";
	$query = $mdl_events->db->query($sql);
	foreach($query->getResultArray() as $row) {
		$events[] = $mdl_events->find($row['id']);
		#$events[] = new \App\Entities\Event($row);

	}
	$this->data['events'] = $events;
		
$this->data['body'] = <<< EOT
<p>These are all the events with entries.</p>
EOT;
	$this->data['base_url'] = 'entries/view';
	return view('events/index', $this->data);
}

public function view($event_id=0) {
	$this->find($event_id);
	$this->data['heading'] .= ' - entries';
	
	if($this->request->getPost('renumber')) {
		$this->mdl_entries->renumber($event_id);
		$this->data['messages'][] = ['Event renumbered', 'success'];
		$this->find($event_id);
	}
		
	if($this->data['event']->clubrets==0) $this->data['messages'][] = ['Returns have not started for this event', 'warning'];
	if($this->data['event']->clubrets==1) $this->data['messages'][] = ['Returns for this event are still open', 'warning'];
	$this->data['breadcrumbs'][] = $this->data['event']->breadcrumb(null, 'admin');
	$this->data['breadcrumbs'][] = ["admin/entries/view/{$event_id}", 'entries'];
	return view('entries/view', $this->data);
}

public function edit($event_id=0) {
	$this->find($event_id);
	
	$filter = []; $flds = ['disid', 'catid'];
	foreach($flds as $fld) $filter[$fld] = $this->request->getGet($fld);
	if(empty($filter['catid']) && $this->data['entries']) {
		$dis = $this->data['entries'][0];
		$filter['disid'] = $dis->id;
		$filter['catid'] = $dis->cats[0]->id;
	}
	
	if($this->request->getPost('save')) {
		// update
		$col_names = ['category_id', 'num', 'name', 'dob', 'user_id'];
		$entries = $this->mdl_entries->cat_entries($filter['catid']);
		$data = [];
		foreach($entries as $entry) {
			foreach($col_names as $col_name) {
				$fldname = "ent{$entry->id}_{$col_name}";
				$fld_val = $this->request->getPost($fldname);
 				$data[$col_name] = $fld_val;
			}
			$this->mdl_entries->update($entry->id, $data);
		}
		// look for new entry
		foreach($col_names as $col_name) {
			$fld_name = "newrow_{$col_name}";
			$fld_val = $this->request->getPost($fld_name);
			$data[$col_name] = $fld_val;
		}
		if($data['name']) {
			$data['category_id'] = $filter['catid'];
			$this->mdl_entries->add_entry($data);
			#d($data);
		}
		
		// read 
		$this->find($event_id);
	}		
	
	// view
	$this->data['breadcrumbs'][] = $this->data['event']->breadcrumb(null, 'admin');
	$this->data['breadcrumbs'][] = ["admin/entries/view/{$event_id}", 'entries'];
	$this->data['breadcrumbs'][] = "admin/entries/edit/{$event_id}";
	
	$this->data['users'] = $this->mdl_entries->evt_users($event_id);
	$this->data['filter'] = $filter;
	return view('entries/edit', $this->data);
}

public function categories($event_id=0) {
	$this->find($event_id);
	$this->data['array_fields'] = ['exercises','music','videos'];
	
	$filter = []; $flds = ['disid'];
	foreach($flds as $fld) $filter[$fld] = $this->request->getGet($fld);
	if(!$filter['disid'] && $this->data['entries']) {
		$filter['disid'] = $this->data['entries'][0]->id;
	}
	
	if($this->request->getPost('save')) {
		// update
		$dis_arr = []; $cat_arr = [];
		$col_names = ['name', 'abbr', 'sort','exercises','music','videos'];
		foreach($this->data['entries'] as $dis) {
			if($dis->id==$filter['disid']) {
				foreach(['name', 'abbr'] as $col_name) {
					$dis_arr[$col_name] = $this->request->getPost("dis{$dis->id}_{$col_name}");
				}
				$this->mdl_entries->update_discipline($dis->id, $dis_arr);
				foreach($dis->cats as $cat) {
					foreach($col_names as $col_name) {
						$fld_name = "cat{$cat->id}_{$col_name}";
						$fld_val = $this->request->getPost($fld_name);
						$cat_arr[$col_name] = $fld_val;
					}
					$empty = trim(implode('', $cat_arr)) ? 0 : 1 ;
					if($empty) { // delete category if empty
						$cat_entries = $this->mdl_entries->cat_entries($cat->id);
						// no delete when there are entries
						if(!count($cat_entries)) {
							$this->mdl_entries->delete_category($cat->id);
						}
					}
					else {
						$cat_arr['id'] = $cat->id;
						$cat_arr['music'] = csv_array($cat_arr['music']);
						$cat_arr['videos'] = csv_array($cat_arr['videos']);
						$entrycat = new \App\Entities\Entrycat($cat_arr);
						$this->mdl_entries->entrycats->save($entrycat);
					}
				}
				// look for new category
				foreach($col_names as $col_name) {
					$fld_name = "newrow_{$col_name}";
					$fld_val = $this->request->getPost($fld_name);
					$cat_arr[$col_name] = $fld_val;
				}
				if($cat_arr['name']) {
					$cat_arr['discipline_id'] = $dis->id;
					$new_id = $this->mdl_entries->entrycats->insert($cat_arr);
					if($new_id) $this->data['messages'][] = ['Created new category', 'success'];
				}
			}
		}
		// read 
		$this->find($event_id);
	}
	// view
	$this->data['breadcrumbs'][] = $this->data['event']->breadcrumb(null, 'admin');
	$this->data['breadcrumbs'][] = ["admin/entries/view/{$event_id}", 'entries'];
	$this->data['breadcrumbs'][] = "admin/entries/categories/{$event_id}";
	
	$this->data['heading'] .= ' - categories';
	$this->data['filter'] = $filter;
	return view('entries/categories', $this->data);
}

public function import($event_id=0) {
	$this->data['columns'] = [
		'dis' => 'discipline (abbr)',
		'cat' => 'category (abbr)',
		'num' => 'number',
		'name' => 'name',
		'club' => 'club (abbr)',
		'dob' => 'DoB (d-M-Y)'
	];
	
	$getPost = trim($this->request->getPost('csv'));
	if($getPost) {
		try {
			// read input
			$input = [];
			$lines = explode("\n", $getPost);
			$map = array_keys($this->data['columns']);
			$count_map = count($map);
			foreach($lines as $line_num=>$line) {
				if(!$line_num) continue;
				$vals = explode("\t", trim($line));
				$count_vals = count($vals);
				if($count_vals!=$count_map) {
					throw new \Exception("{$count_vals} columns on line {$line_num}");
				}
				foreach($map as $key=>$dest) {
					$row[$dest] = $vals[$key];
				}
				$input[] = $row;
			}
			if(!$input) throw new \Exception("No input");
			# d($input);
			
			// parse input v2
			$dis_key = -1;
			$dis_name = null;
			$discat = [];
			foreach($input as $line) {
				if($line['dis']!==$dis_name) {
					$dis_key++;
					$dis_name = $line['dis'];
					$discat[$dis_key] = [
						'name' => $dis_name,
						'cats' => []
					];
					$cat_key = -1;
					$cat_name = null;
				}
				if($line['cat']!==$cat_name) {
					$cat_key++;
					$cat_name = $line['cat'];
					$discat[$dis_key]['cats'][$cat_key] = [
						'name' => $cat_name,
						'entries' => []
					];
				}
				$discat[$dis_key]['cats'][$cat_key]['entries'][] = $line;		
			}
			# d($discat);
			# throw new \Exception('not finished yet');
			
			// delete existing data
			$this->mdl_entries->delete_event($event_id);
			
			// import new data
			$mdl_clubs = new \App\Models\Users;
			$current_user = $mdl_clubs->find(session('user_id'));
			foreach($discat as $dis) {
				$arr = [
					'event_id' => $event_id, 
					'name' => $dis['name']
				];
				$dis_id = $this->mdl_entries->add_discipline($arr);
				if(!$dis_id) {
					$errors = $this->mdl_entries->errors();
					$errors[] = "Couldn't create discipline '{$dis['name']}'"; 
					throw new \Exception(implode('<br>', $errors));
				}
				foreach($dis['cats'] as $sort=>$cat) {
					$arr = [
						'discipline_id' => $dis_id, 
						'name' => $cat['name'], 
						'sort' => str_pad($sort, 3, '0', STR_PAD_LEFT)
					];
					$cat_id = $this->mdl_entries->entrycats->insert($arr);
					if(!$cat_id) {
						$errors = $this->mdl_entries->errors();
						$errors[] = "Couldn't create category '{$cat['name']}'"; 
						throw new \Exception(implode('<br>', $errors));
					}
					foreach($cat['entries'] as $entry) {
						$entry['category_id'] = $cat_id;
						$entry['dob'] = date('Y-m-d', strtotime($entry['dob']));
						$club = $mdl_clubs->withDeleted()->where('name', $entry['club'])->first();
						if(!$club) $club = $mdl_clubs->withDeleted()->where('abbr', $entry['club'])->first();
						if($club) {
							$user_id = $club->id;
						}
						else {
							$data = [
								'abbr' => $entry['club'],
								'name' => str_pad($entry['club'], 6, '_'),
								'password' => random_bytes(12),
								'email' => $current_user->email
							];
							$club = new \App\Entities\User($data);
							if($mdl_clubs->insert($data)) {
								$user_id = $mdl_clubs->db->insertID();
							}
							else {
								$errors = $mdl_clubs->errors();
								$errors[] = "Couldn't create club '{$entry['club']}'"; 
								throw new \Exception(implode('<br>', $errors));
							}
						}
						#unset($entry['club']);
						$entry['user_id'] = $user_id;
						
						#d($entry);
						# d($club);
						
						$this->mdl_entries->add_entry($entry);
					}
				}
			}
			
			$this->data['messages'][] = ['Import successful', 'success'];			
		}
		catch (\Exception $e) {
			# d($e);
			$this->data['messages'][] = [$e->getMessage(), 'danger'];
		}
	}

	// read from database
	$this->find($event_id);
		
	$this->data['breadcrumbs'][] = $this->data['event']->breadcrumb(null, 'admin');
	$this->data['breadcrumbs'][] = ["admin/entries/view/{$event_id}", 'entries'];
	$this->data['breadcrumbs'][] = ["admin/entries/import/{$event_id}", 'import'];

	return view('entries/import', $this->data);
}

public function scoreboard($event_id=0) {
	$this->find($event_id);
	
	$usr_model = new \App\Models\Users();
	$users = [];
	foreach($this->data['entries'] as $dis) { 
		foreach($dis->cats as $cat) { 
			foreach($cat->entries as $entry) {
				$user_id = $entry->user_id;
				if(empty($users[$user_id])) {
					$users[$user_id] = $usr_model->withDeleted()->find($user_id);
				}
			}	
		}
	}
	$this->data['users'] = $users;
	
	// view
	#$this->response->setHeader('Content-Type', 'text/plain');
	#$this->response->setHeader('Content-Type', 'application/sql');
	#$this->response->setHeader('Content-Disposition', 'attachment; filename=scoreboard.sql');
	# return view('entries/sb-sql', $this->data);
	return view('entries/export-csv', $this->data);
}

}
