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
	$tbody = [];
	foreach($this->data['users'] as $id=>$user) {
		$entcount = $counts[$id] ?? 0 ;
		if(!$entcount) $error = true;
		$tbody[] = [
			'state' => $user->state ? 1 : 0 ,
			'name' => $user->name,
			'abbr' => $user->abbr,
			'email' => $user->email,
			'count' => $entcount
		];
	}
	if($error) $this->data['messages'][] = ['Inconsistent data', 'danger'];
	$download = $this->request->getPost('download');
	if($download=='clubs') {
		
		
		
		return $this->download(['export'=>$tbody], 'table', $download);
	}
		
	$this->data['tbody'] = $tbody;
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
	
	$batch = $this->request->getPost('batch');
	if($batch) {
		$update = null;
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
			if($batch=='runorder') {
				$runorder = $this->request->getPost();
				unset($runorder['runorder']);
				unset($runorder['batch']);
				$update = ['runorder' => json_encode($runorder)];
			}
			if($batch=='catmerge') {
				$category_id = intval($this->request->getPost('category_id'));
				if($category_id) {
					$update = ['category_id'=>$category_id];
				}
			}
		}
		if($update) {	
			$this->ent_model->update($ent_ids, $update);
			// read 
			$this->find($event_id);
		}
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
	
	if($this->request->getPost('merge')) {
		$source = intval($this->request->getPost('source'));
		$dest = intval($this->request->getPost('dest'));
		if($source && $dest && $source!==$dest) {
			$where = ['category_id'=>$source];
			$ent_ids = [];
			foreach($this->ent_model->where($where)->findAll() as $tmp) $ent_ids[] = $tmp->id;
			if($ent_ids) {
				$update = ['category_id'=>$dest];
				$this->ent_model->update($ent_ids, $update);
				// read 
				$this->find($event_id);
				$this->data['messages'][] = ['Merged categories', 'success'];
			}
		}
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

public function export($event_id=0, $download=0) {
	$this->find($event_id);

	// build user table
	$usr_model = new \App\Models\Users();
	$ent_users = [];
	foreach($this->data['entries'] as $dis) { 
		foreach($dis->cats as $cat) { 
			foreach($cat->entries as $entry) {
				$user_id = $entry->user_id;
				if($user_id && empty($ent_users[$user_id])) {
					$ent_users[$user_id] = $usr_model->withDeleted()->find($user_id);
				}
			}	
		}
	}	
	
	// build export table
	$source = $this->request->getGet('source');
	$export_table = []; 
	$this->data['headings'] = [];
	switch($source) {
		case 'score_table':
		$scoreboard = new \App\ThirdParty\scoreboard;
		$exesets = [];
		foreach($scoreboard->get_exesets() as $exeset) {
			$key = $exeset['SetId'];
			$exesets[$key] = $exeset['children'];
		}
		
		foreach($this->data['entries'] as $dis) {
			foreach($dis->cats as $cat) {
				$tr = [
					'dis' => $dis->name,
					'cat' => $cat->name,
					'num' => 'num',
					'club' => 'club',
					'name' => 'name'
				];
				
				$exeset = $exesets[$cat->exercises] ?? [] ;
				foreach($exeset as $exe) {
					$tr[$exe['ShortName']] = '';
				}
				$tr['Tot'] = '';
				$tr['Pos'] = '';
																
				foreach($cat->entries as $rowkey=>$entry) {
					$tr['num'] = $entry->num;
					$tr['club'] = $ent_users[$entry->user_id]->abbr ?? '?';
					$tr['name'] = $entry->name;
					$export_table[] = $tr;
				}
			}
		}
		$this->data['layout'] = 'cattable';
		$this->data['table_header'] = true;
		$this->data['headings'] = ['dis', 'cat'];
		break;
		
		case 'entries':
		foreach($this->data['entries'] as $dis) {
			foreach($dis->cats as $cat) {
				foreach($cat->entries as $rowkey=>$entry) {
					$export_table[] = [
						'dis' => $dis->name,
						'cat' => $cat->name,
						'num' => $entry->num,
						'club' => $ent_users[$entry->user_id]->abbr ?? '?',
						'name' => $entry->name
					];
				}
			}
		}
		$this->data['layout'] = 'cattable';
		$this->data['table_header'] = false;
		$this->data['headings'] = ['dis', 'cat'];
		break;
		
		case 'entry_list':
		$sort = [];
		foreach($this->data['entries'] as $dis) {
			foreach($dis->cats as $cat) {
				foreach($cat->entries as $rowkey=>$entry) {
					$export_table[] = [
						'num' => $entry->num,
						'name' => $entry->name
					];
					$sort[] = $entry->num;
				}
			}
		}
		array_multisort($sort, $export_table);

		$this->data['layout'] = 'table';
		$this->data['table_header'] = false;
		break;
		
		case 'run_order':
		$sort = [];
		foreach($this->data['entries'] as $dis) {
			foreach($dis->cats as $cat) {
				foreach($cat->entries as $rowkey=>$entry) {
					$export_table[] = [
						'runorder' => implode(', ', $entry->get_rundata('export')),
						'dis' => $dis->name,
						'cat' => $cat->name,
						'num' => $entry->num,
						'club' => $ent_users[$entry->user_id]->abbr ?? '?',
						'name' => $entry->name
					];
					$sort[] = [
						$entry->get_rundata('order'),
						$dis->abbr,
						$cat->sort,
						$entry->num
					];
				}
			}
		}
		array_multisort($sort, $export_table);
		# d($sort);
				
		$this->data['layout'] = 'cattable';
		$this->data['table_header'] = false;
		$this->data['headings'] = ['runorder', 'dis', 'cat'];
		break;
		
		default:
		$source = 'scoreboard';
		$sort = [];
		$row = []; 
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
							'name' => $ent_users[$entry->user_id]->name ?? '??',
							'shortName' => $ent_users[$entry->user_id]->abbr ?? '?'
						],
						'number' => $entry->num,
						'title' => $entry->name,
						'dob' => $entry->dob
					];
					$row['order'] = '';#$entry->get_rundata('order');
					$row['run'] = $entry->get_rundata('export');
					$export_table[] = $row;
					// NB: same sort as run_order
					$sort[] = [
						$entry->get_rundata('order'),
						$dis->abbr,
						$cat->sort,
						$entry->num
					];
				}		
				// end cat 
			} 
			// end dis  
		} 
		// end entries
		$this->data['layout'] = 'table';
		array_multisort($sort, $export_table);
		// preserve sort order for Kev
		foreach($export_table as $rowkey=>$row) {
			$export_table[$rowkey]['order'] = sprintf('%04d', $rowkey + 1);
			# $export_table[$rowkey]['order'] = implode('-', $sort[$rowkey]);
		}
	}
	$this->data['export'] = $export_table;
	
	$action = $this->request->getGet('action');
	if($action=='download') {
		return $this->download($this->data, $this->data['layout'], $source);
	}
	
	// view
	$this->data['source'] = $source;
	$this->data['heading'] .= ' - ' . humanize($source);
	$this->data['breadcrumbs'][] = ["admin/entries/export/{$event_id}", 'export'];

	// valid sources
	$arr = ['scoreboard', 'score_table', 'run_order', 'entries', 'entry_list'];
	$this->data['source_opts'] = [];
	foreach($arr as $key) {
		$this->data['source_opts'][$key] = humanize($key);
	}

	return view('entries/export', $this->data);			
}

}
 