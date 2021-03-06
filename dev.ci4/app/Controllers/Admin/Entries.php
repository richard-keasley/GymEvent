<?php namespace App\Controllers\Admin;

class Entries extends \App\Controllers\BaseController {
	
private $ent_model = null;
private $evt_model = null;
private $found = false;

function __construct() {
	$this->data['breadcrumbs'][] = 'admin';
	$this->data['breadcrumbs'][] = 'admin/events';
	$this->ent_model = new \App\Models\Entries;
	$this->evt_model = new \App\Models\Events;
	$this->data['title'] = "entries";
	$this->data['heading'] = "Event entries - admin";
}
	
private function find($event_id, $orderby='num') {
	$this->data['event'] = $this->evt_model->find($event_id);
	if(!$this->data['event']) throw new \RuntimeException("Can't find event $event_id", 404);
	$this->data['entries'] = $this->ent_model->evt_discats($event_id, 1, $orderby);
	$this->data['title'] = $this->data['event']->title;
	$this->data['heading'] = $this->data['event']->title;
	if(!$this->found) {
		$this->data['breadcrumbs'][] = $this->data['event']->breadcrumb(null, 'admin');
		$this->data['breadcrumbs'][] = ["admin/entries/view/{$event_id}", 'entries'];
	}
	$this->found = true;
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

public function view($event_id=0, $format='plain') {
	switch($format) {
		case 'dob': $orderby = 'dob'; break;
		default: $orderby = 'num';
	}
	
	$this->find($event_id, $orderby);
	$this->data['heading'] .= ' - entries';
	
	if($this->request->getPost('renumber')) {
		$this->ent_model->renumber($event_id);
		$this->data['messages'][] = ['Event renumbered', 'success'];
		$this->find($event_id, $orderby);
	}
	
	// view
	foreach($this->ent_model->get_errors($event_id) as $error) {
		$this->data['messages'][] = $error;
	}
	$this->data['format'] = $format;
	$this->data['users'] = $this->ent_model->evt_users($event_id);
	if($this->data['event']->clubrets==0) $this->data['messages'][] = ['Returns have not started for this event', 'warning'];
	if($this->data['event']->clubrets==1) $this->data['messages'][] = ['Returns for this event are still open', 'warning'];
	return view('entries/view', $this->data);
}

public function clubs($event_id=0) {
	$this->find($event_id);
	$this->data['heading'] .= ' - clubs';
	$this->data['breadcrumbs'][] = ["admin/entries/clubs/{$event_id}", 'clubs'];
	
	$this->data['users'] = $this->ent_model->evt_users($event_id);

	$counts = [];
	foreach($this->data['entries'] as $dis) { 
		foreach($dis->cats as $cat) {
			foreach($cat->entries as $entry) {
				$key = $entry->user_id;
				if(empty($counts[$key])) $counts[$key] = 0;
				$counts[$key]++;
			}
		}
	}
	$this->data['entcount'] = array_sum($counts);
	
	$error = false;
	foreach($this->data['users'] as $id=>$username) {
		$entcount = $counts[$id] ?? 0 ;
		if(!$entcount) $error = true;
		$this->data['users'][$id]->entcount = $entcount;
	}
	if($error) $this->data['messages'][] = ['Inconsistent data', 'danger'];
	
	return view('entries/clubs', $this->data);
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
	# d($filter);
		
	if($this->request->getPost('save')) {
		// update
		$col_names = ['category_id', 'num', 'name', 'dob', 'user_id'];
		$entries = $this->ent_model->cat_entries($filter['catid']);
		$data = [];
		foreach($entries as $entry) {
			foreach($col_names as $col_name) {
				$fldname = "ent{$entry->id}_{$col_name}";
				$fld_val = $this->request->getPost($fldname);
				$data[$col_name] = $fld_val;
			}
			$runorder = [];
			foreach($entry->runorder as $key=>$val) {
				$fldname = "ent{$entry->id}_run_{$key}";
				$fld_val = $this->request->getPost($fldname);
				$runorder[$key] = $fld_val;
			}
			$data['runorder'] = json_encode($runorder);
			
			# d($data['name']);
			
			if($data['name']=='#delrow') {
				$this->ent_model->delete($entry->id, 1);
			}
			else {
				$this->ent_model->update($entry->id, $data);
			}
		}
		
		// look for new entry
		$data = [];
		foreach($col_names as $col_name) {
			$fld_name = "newrow_{$col_name}";
			$fld_val = $this->request->getPost($fld_name);
			$data[$col_name] = $fld_val;
		}
		if($data['name']) {
			$data['category_id'] = $filter['catid'];
			$this->ent_model->add_entry($data);
			#d($data);
		}
		
		// read 
		$this->find($event_id);
	}
	
	if($this->request->getPost('runorder')) {
		// select which entries to update
		$ent_ids = [];
		foreach($this->data['entries'] as $dis) {
			foreach($dis->cats as $cat) {
				if($cat->id===$filter['catid']) {
					foreach($cat->entries as $cat_entry) {
						$ent_ids[] = $cat_entry->id;
					}
				}
			}
		}
		if($ent_ids) {
			$runorder = $this->request->getPost();
			unset($runorder['runorder']);
			$data = ['runorder' => json_encode($runorder)];
			$this->ent_model->update($ent_ids, $data);
		}
		// read 
		$this->find($event_id);
	}
	
	// view
	foreach($this->ent_model->get_errors($event_id) as $error) {
		$this->data['messages'][] = $error;
	}
	$this->data['breadcrumbs'][] = "admin/entries/edit/{$event_id}";
	$this->data['users'] = $this->ent_model->evt_users($event_id);
	$this->data['filter'] = $filter;
	return view('entries/edit', $this->data);
}

public function categories($event_id=0) {
	$this->find($event_id);
	
	$col_names = ['name', 'abbr', 'sort', 'exercises'];
	$array_fields = [];
	if(\App\Libraries\Track::enabled()) {
		$col_names[] = 'music';
		$array_fields[] = 'music';
	}
	if(\App\Libraries\Video::enabled()) {
		$col_names[] = 'videos';
		$array_fields[] = 'videos';
	}	
	
	$filter = []; $flds = ['disid'];
	foreach($flds as $fld) $filter[$fld] = $this->request->getGet($fld);
	if(!$filter['disid'] && $this->data['entries']) {
		$filter['disid'] = $this->data['entries'][0]->id;
	}
	
	if($this->request->getPost('save')) {
		// update
		$dis_arr = []; $cat_arr = [];
		foreach($this->data['entries'] as $dis) {
			if($dis->id==$filter['disid']) {
				foreach(['name', 'abbr'] as $col_name) {
					$dis_arr[$col_name] = $this->request->getPost("dis{$dis->id}_{$col_name}");
				}
				$this->ent_model->update_discipline($dis->id, $dis_arr);
				foreach($dis->cats as $cat) {
					foreach($col_names as $col_name) {
						$fld_name = "cat{$cat->id}_{$col_name}";
						$fld_val = trim($this->request->getPost($fld_name));
						$cat_arr[$col_name] = $fld_val;
					}
					
					if($cat_arr['name']=='#delrow') {
						$cat_entries = $this->ent_model->cat_entries($cat->id);
						// no delete when there are entries
						if(!count($cat_entries)) {
							$this->ent_model->delete_category($cat->id);
						}
					}
					else {
						$cat_arr['id'] = $cat->id;
						foreach($array_fields as $array_field) {
							$fld_val = [];
							foreach(explode(',', str_replace(' ', '', $cat_arr[$array_field])) as $val) {
								if($val) $fld_val[] = $val;
							}
							$cat_arr[$array_field] = $fld_val;
						}
						$entrycat = new \App\Entities\Entrycat($cat_arr);
						$this->ent_model->entrycats->save($entrycat);
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
					$new_id = $this->ent_model->entrycats->insert($cat_arr);
					if($new_id) $this->data['messages'][] = ['Created new category', 'success'];
				}
			}
		}
		// read 
		$this->find($event_id);
	}
	
	// view
	$this->data['breadcrumbs'][] = "admin/entries/categories/{$event_id}";
	
	$this->data['col_names'] = $col_names;
	
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
		'club' => 'club name',
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
			$this->ent_model->delete_event($event_id);
			
			// import new data
			$usr_model = new \App\Models\Users;
			$current_user = $usr_model->find(session('user_id'));
			foreach($discat as $dis) {
				$arr = [
					'event_id' => $event_id, 
					'name' => $dis['name']
				];
				$dis_id = $this->ent_model->add_discipline($arr);
				if(!$dis_id) {
					$errors = $this->ent_model->errors();
					$errors[] = "Couldn't create discipline '{$dis['name']}'"; 
					throw new \Exception(implode('<br>', $errors));
				}
				foreach($dis['cats'] as $sort=>$cat) {
					$arr = [
						'discipline_id' => $dis_id, 
						'name' => $cat['name'], 
						'sort' => str_pad($sort, 3, '0', STR_PAD_LEFT)
					];
					$cat_id = $this->ent_model->entrycats->insert($arr);
					if(!$cat_id) {
						$errors = $this->ent_model->errors();
						$errors[] = "Couldn't create category '{$cat['name']}'"; 
						throw new \Exception(implode('<br>', $errors));
					}
					foreach($cat['entries'] as $entry) {
						$entry['category_id'] = $cat_id;
						$entry['dob'] = date('Y-m-d', strtotime($entry['dob']));
						$club = $usr_model->withDeleted()->where('name', $entry['club'])->first();
						if(!$club) $club = $usr_model->withDeleted()->where('abbr', $entry['club'])->first();
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
							if($usr_model->insert($data)) {
								$user_id = $usr_model->db->insertID();
								$this->data['messages'][] = ["Created club {$data['name']}", 'success'];		
							}
							else {
								$errors = $usr_model->errors();
								$errors[] = "Couldn't create club '{$entry['club']}'"; 
								throw new \Exception(implode('<br>', $errors));
							}
						}
						#unset($entry['club']);
						$entry['user_id'] = $user_id;
						
						#d($entry);
						# d($club);
						
						$this->ent_model->add_entry($entry);
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
		
	$this->data['breadcrumbs'][] = ["admin/entries/import/{$event_id}", 'import'];

	return view('entries/import', $this->data);
}

public function export($event_id=0, $format='view') {
	$this->find($event_id);

	// build user table
	$usr_model = new \App\Models\Users();
	$this->data['users'] = [];
	foreach($this->data['entries'] as $dis) { 
		foreach($dis->cats as $cat) { 
			foreach($cat->entries as $entry) {
				$user_id = $entry->user_id;
				if($user_id && empty($this->data['users'][$user_id])) {
					$this->data['users'][$user_id] = $usr_model->withDeleted()->find($user_id);
				}
			}	
		}
	}	
	
	// build export table
	$this->data['export'] = []; $row = [];
	foreach($this->data['entries'] as $dis) { 
		$row['dis'] = [
			'name' => $dis->name,
			'abbr' => $dis->abbr
		];
		foreach($dis->cats as $cat) { 
			$row['cat'] = [
				'name' => $cat->name,
				'abbr' => $cat->abbr,
				'order' => $cat->sort,
				'setid' => $cat->exercises
			];
			foreach($cat->entries as $entry) {
				$row['entry'] = [
					'club' => [
						'name' => $this->data['users'][$entry->user_id]->name ?? '??',
						'shortName' => $this->data['users'][$entry->user_id]->abbr ?? '?'
					],
					'number' => $entry->num,
					'title' => $entry->name,
					'dob' => $entry->dob
				];
				$row['order'] = $entry->get_rundata('order');
				$row['run'] = $entry->get_rundata('export');
				
				$this->data['export'][] = $row;
			}		
			// end cat 
		} // end dis  
	} // end entries

	// check format requested 
	$filetypes = [
		'default' => '',
		'sql' => 'sql.txt',
		'csv' => 'csv', 
		'scoretable' => 'csv',
		'run' => ''
	];
	// use default if not valid
	if(!isset($filetypes[$format])) $format = array_key_first($filetypes);
	$filetype = $filetypes[$format];
	
	// download if filetype is set
	if($filetype) {
		$response = view("entries/export-{$format}", $this->data);
		$filetitle = strtolower(preg_replace('#[^A-Z0-9]#i', '_', $this->data['event']->title));
		# return UTF8_BOM . '<pre>' . $response . '</pre>';
		return $this->response->download("{$filetitle}.{$filetype}", UTF8_BOM . $response);
	}
			
	$this->data['breadcrumbs'][] = ["admin/entries/export/{$event_id}", 'export'];
	$suffix = $format=='run' ? 'run order' : 'export';
	$this->data['heading'] .= " - {$suffix}";
	$this->data['format'] = $format;
	return view('entries/export', $this->data);	
}

}
 