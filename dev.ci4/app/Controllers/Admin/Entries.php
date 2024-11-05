<?php namespace App\Controllers\Admin;

use \App\Libraries\Teamtime as tt_lib;

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
	if(!$this->data['event']) {
		$message = "Can't find event {$event_id}";
		throw \App\Exceptions\Exception::not_found($message);
	}
	
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
	$this->data['body'] = 'admin_entries';
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
	$this->data['users'] = $this->data['event']->users();
	if($this->data['event']->clubrets==0) $this->data['messages'][] = ['Returns have not started for this event', 'warning'];
	if($this->data['event']->clubrets==1) $this->data['messages'][] = ['Returns for this event are still open', 'warning'];
	return view('entries/view', $this->data);
}

public function clubs($event_id=0) {
	$this->find($event_id);
	$this->data['heading'] .= ' - clubs';
	$this->data['breadcrumbs'][] = ["admin/entries/clubs/{$event_id}", 'clubs'];
	$this->data['users'] = $this->data['event']->users();
	
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
	
	$tbody = []; $sort = [];
	$not_found = '<i title="user not found" class="bi-exclamation-triangle-fill text-danger"></i>';
	foreach($counts as $user_id=>$count) {
		$user = $this->data['users'][$user_id] ?? null;
		if($user) $state = $user->state ? 1 : 0;
		else $state = 1;
		$tbody[] = [
			'state' => $state,
			'name' => $user->name ?? $not_found,
			'abbr' => $user->abbr ?? $not_found,
			'email' => $user->email ?? $not_found,
			'count' => $count
		];
		$sort[] = $user->name ?? '' ;
	}
	array_multisort($sort, $tbody);
		
	$download = $this->request->getGet('dl');
	if($download=='clubs') {
		$export = ['export' => $tbody];
		return $this->download($export, 'table', $download);
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
		$col_names = ['category_id', 'user_id', 'num', 'name', 'dob', 'guest', 'opt'];
		$entries = $this->ent_model->cat_entries($filter['catid']);
		$data = [];
		foreach($entries as $entry) {
			foreach($col_names as $col_name) {
				$fld_name = "ent{$entry->id}_{$col_name}";
				$fld_val = $this->request->getPost($fld_name);
				$data[$col_name] = filter_string($fld_val);
			}
			$runorder = [];
			foreach($entry->runorder as $key=>$val) {
				$fld_name = "ent{$entry->id}_run_{$key}";
				$fld_val = $this->request->getPost($fld_name);
				$runorder[$key] = filter_string($fld_val);
			}
			$data['runorder'] = json_encode($runorder);
			# d($data);
			
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
			$data[$col_name] = filter_string($fld_val);
		}
		if($data['name']) {
			$data['category_id'] = $filter['catid'];
			$this->ent_model->add_entry($data);
		}
		
		// read 
		$this->find($event_id);
	}
	
	if($this->request->getPost('update_exeset')) {
		$exercises = intval($this->request->getPost('exercises'));
		if($exercises) {
			$cat_arr = [
				'id' => $filter['catid'],
				'exercises' => $exercises
			];
			// update
			$entrycat = new \App\Entities\Entrycat($cat_arr);
			$this->ent_model->entrycats->save($entrycat);
			// read 
			$this->find($event_id);
		}
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
	
	// filter which entries to show
	$this->data['cat_entries'] = [];
	$this->data['exeset_id'] = 0;
	$opt_count = 0;
	foreach($this->data['entries'] as $dis) {
		foreach($dis->cats as $cat) {
			if($cat->id===$filter['catid']) {
				$this->data['exeset_id'] = $cat->exercises;
				$this->data['cat_entries'] = $cat->entries;
				foreach($cat->entries as $entry) {
					if($entry->opt) $opt_count++;
				}
			}
		}
	}
			
	// view
	foreach($this->ent_model->get_errors($event_id) as $error) {
		$this->data['messages'][] = $error;
	}
	$this->data['breadcrumbs'][] = "admin/entries/edit/{$event_id}";
	$this->data['user_options'] = $this->data['event']->users('clubrets', false);
	$this->data['filter'] = $filter;
	
	// sort requested?
	$allowed = ['name', 'club', 'dob'];
	$sort_field = $this->request->getGet('sort');
	if(!in_array($sort_field, $allowed)) $sort_field = false;
	$this->data['sort'] = $sort_field;
	if($sort_field) {
		$orderby = [];
		foreach($this->data['cat_entries'] as $cat_entry) {
			$orderby[] = match($sort_field) {
				'club' => $this->data['user_options'][$cat_entry->user_id] ?? '',
				default => $cat_entry->{$sort_field}
			};
		}
		# d($orderby, $this->data['cat_entries']);
		array_multisort($orderby, $this->data['cat_entries']);
		# d($orderby, $this->data['cat_entries']);
	}
	
	if($opt_count && $opt_count!=count($this->data['cat_entries'])) {
		$this->data['messages'][] = ['Entry options should to be entered for <em>all</em> entries or <em>none</em>.', 'warning'];
	}
	
	if(!$this->data['entries']) {
		$this->data['messages'][] = ['There are no entries in this event', 'danger'];
		return view('entries/view', $this->data);
	}
		
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
					$cat_arr['sort'] = sprintf('%03d', $cat_arr['sort']);
					
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
					$cat_arr['sort'] = sprintf('%03d', $cat_arr['sort']);
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
		'cat' => 'category (full)',
		'num' => 'number',
		'name' => 'name',
		'club' => 'club',
		'dob' => 'DoB (d-M-Y)',
		'runorder' => 'Running order (int-int-int)', // optional
		'exeset' => 'Exercise set (int)' // optional
	];

	$getPost = $this->request->getPost('csv');
	if($getPost) {
		try {
			// use this code for custom exceptions
			$ex_code = 999;
			// read input
			$lines = explode("\n", $getPost);
			$map = array_keys($this->data['columns']);
			$optional = ['exeset', 'runorder'];
			$count_map = count($map) - count($optional);
			
			$input = []; $row = [];
			foreach($lines as $line_num=>$line) {
				$line = trim($line);
				if(!$line_num) continue; // skip first line
				if(!$line) continue; // skip blank lines
				
				$vals = preg_split("/ *\t */", trim($line));
				$count_vals = count($vals);
				if($count_vals<$count_map) {
					throw new \Exception("{$count_vals} columns on line {$line_num}", $ex_code);
				}
				foreach($map as $src=>$dest) {
					$val = $vals[$src] ?? '';
					if(!in_array($dest, $optional) && !$val) {
						throw new \Exception("Empty {$dest} on line {$line_num}", $ex_code);
					}
					$row[$dest] = $val;
				}
				
				$input[] = $row;
			}
			if(!$input) throw new \Exception("No input", $ex_code);
			# d($input);
			
			// parse input
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
						'exercises' => $line['exeset'],
						'entries' => []
					];
				}

				$val = new \datetime($line['dob']);			
				$line['dob'] = $val->format('Y-m-d');
				
				$val = explode('-', $line['runorder']);
				$val = [
					'rnd' => $val[0] ?? null,
					'rot' => $val[1] ?? null,
					'exe' => $val[2] ?? null
				];
				$line['runorder'] = $val; # json_encode($val);
				# d($val);
								
				$discat[$dis_key]['cats'][$cat_key]['entries'][] = $line;		
			}
			# d($discat); throw new \Exception('not finished yet', $ex_code);
			
			$ret_model = new \App\Models\Clubrets;
			$usr_model = new \App\Models\Users;
			
			// delete existing data
			$this->ent_model->delete_event($event_id);
			$ret_model->delete_event($event_id);
			
			// import new data
			$current_user = $usr_model->find(session('user_id'));
			$ent_count = 0;
			$clubs = [];
			foreach($discat as $dis) {
				$arr = [
					'event_id' => $event_id, 
					'name' => $dis['name']
				];
				$dis_id = $this->ent_model->add_discipline($arr);
				if(!$dis_id) {
					$errors = $this->ent_model->errors();
					$errors[] = "Couldn't create discipline '{$dis['name']}'"; 
					throw new \Exception(implode('<br>', $errors), $ex_code);
				}
				foreach($dis['cats'] as $sort=>$cat) {
					$arr = [
						'discipline_id' => $dis_id, 
						'name' => $cat['name'], 
						'exercises' => $cat['exercises'], 
						'sort' => str_pad($sort, 3, '0', STR_PAD_LEFT)
					];
					$cat_id = $this->ent_model->entrycats->insert($arr);
					if(!$cat_id) {
						$errors = $this->ent_model->errors();
						$errors[] = "Couldn't create category '{$cat['name']}'"; 
						throw new \Exception(implode('<br>', $errors), $ex_code);
					}
					foreach($cat['entries'] as $entry) {
						$entry['category_id'] = $cat_id;

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
								throw new \Exception(implode('<br>', $errors), $ex_code);
							}
						}
						$entry['user_id'] = $user_id;
						$clubs[$user_id] = $entry['club'];
						
						# d($club);
						# d($entry);
						
						$this->ent_model->add_entry($entry);
						$ent_count++;
					}
				}
			}
			
			// add an empty return for each club
			foreach($clubs as $user_id=>$club_name) {
				$clubret = [
					'event_id' => $event_id, 
					'user_id'=>$user_id
				];
				$clubret = new \App\Entities\Clubret($clubret);
				$ret_model->insert($clubret);
			}
			
			$message = sprintf('%u entries, %u clubs', $ent_count, count($clubs));
			$this->data['messages'][] = ["Import successful - {$message}", 'success'];	
		}
		catch(\Exception $e) {
			$code = $e->getCode();
			$error = $e->getMessage();
			// custom error or PHP error 
			$message = $code===$ex_code ? 
				$error : 
				sprintf('Error (%s): %s - line %u', $code, $error, $e->getLine()) ;
			$this->data['messages'][] = [$message, 'danger'];
		}
	}

	// read from database
	$this->find($event_id);
			
	$this->data['breadcrumbs'][] = ["admin/entries/import/{$event_id}", 'import'];

	return view('entries/import', $this->data);
}

public function export($event_id=0, $download=0) {
	$this->find($event_id);
	$tg_id = tt_lib::get_value('settings', 'event_id');
	$teamgym = ($tg_id==$event_id);
	
	$source = $this->request->getGet('source');
	
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
	$export_table = [];
	$this->data['tfoot'] = [];
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
					'Num' => 'num',
					'Club' => 'club',
					'Name' => 'name'
				];
				$exeset = $exesets[$cat->exercises] ?? [] ;
				$exe_count = count($exeset);
				
				if($exe_count) {
					foreach($exeset as $exe) {
						$tr[$exe['ShortName']] = '';
					}
					$last = count($cat->entries) - 1;
				}
													
				foreach($cat->entries as $rowkey=>$entry) {
					$tr['Num'] = $entry->num;
					$tr['Club'] = $ent_users[$entry->user_id]->abbr ?? '?';
					$tr['Name'] = $entry->name;
					if($entry->guest) $tr['Name'] .= ' (G)';
				
					if($exe_count) {
						$tr['Tot'] = "=SUM([-{$exe_count}]:[-1])";

						$tot_pos = -1;
						$row_start = '$' . 0 - $rowkey;
						$row_last = '$' . $last - $rowkey;
						$tr['Pos'] = "=RANK([{$tot_pos}],[{$tot_pos},{$row_start}]:[{$tot_pos},{$row_last}])";
					}
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
					$tr = [
						'dis' => $dis->name,
						'cat' => $cat->name,
						'num' => $entry->num,
						'club' => $ent_users[$entry->user_id]->abbr ?? '?',
						'name' => $entry->name
					];
					if($entry->guest) $tr['name'] .= ' (G)';
				
					$export_table[] = $tr;
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
					if(!$rowkey) $has_opt = $entry->opt;
					$tr = [
						'dis' => $dis->name,
						'cat' => $cat->name,
						'num' => $entry->num,
						'club' => $ent_users[$entry->user_id]->abbr ?? '?',
						'name' => $entry->name,
						'DoB' => $entry->dob,
						'opt' => humanize($entry->opt),
					];
						
					$export_table[] = $tr;
					$sort[] = $entry->num;
				}
			}
		}
		array_multisort($sort, $export_table);

		$this->data['layout'] = 'table';
		$this->data['table_header'] = true;
		break;
		
		case 'running_order':
		if($teamgym) {
			$progtable = tt_lib::get_value('progtable');
			if($progtable) {
				$teams = [];
				foreach($this->data['entries'] as $dis) {
					foreach($dis->cats as $cat) {
						foreach($cat->entries as $rowkey=>$entry) {
							$teams[$entry->num] = $entry->name;
						}
					}
				}
				
				// first row provides keys
				$keys = array_shift($progtable);
				$export_row = [];
				foreach($progtable as $rowkey=>$row) {
					$runmode = $row[0];
					foreach($keys as $colnum=>$key) {
						// ignore run mode
						if($colnum) {
							$val = $row[$colnum] ?? '';
							switch($runmode) {
								case 't':
								$val = humanize($val);
								break;
							
								default:
								$team = $teams[$val] ?? null ;
								if($team) $val = "{$val}, {$team}" ;
							}
							$export_row[$key] = $val;
						}
					}
					$export_table[] = $export_row;
				}
				# d($export_table);
			}
			$this->data['layout'] = 'table';
		}
		else {
			$sort = [];
			foreach($this->data['entries'] as $dis) {
				foreach($dis->cats as $cat) {
					foreach($cat->entries as $rowkey=>$entry) {
						$row = [
							'runorder' => implode(', ', $entry->get_rundata('runorder')),
							'dis' => $dis->name,
							'cat' => $cat->name,
							'num' => $entry->num,
							'club' => $ent_users[$entry->user_id]->abbr ?? '?',
							'name' => $entry->name
						];
						if($entry->guest) $row['name'] .= ' (G)';
						
						if(!$rowkey) $has_opt = $entry->opt;
						if($has_opt) $row['opt'] = humanize($entry->opt);
						$export_table[] = $row;
						
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
		}
		break;
		
		case 'round_summary':
		$sort = [];
		foreach($this->data['entries'] as $dis) {
			foreach($dis->cats as $cat) {
				foreach($cat->entries as $rowkey=>$entry) {
					$this_sort = [
						$entry->get_rundata('order'),
						$dis->abbr,
						$cat->sort
					];
					$key = array_search($this_sort, $sort);
					if($key===false) {
						$key = count($sort);
						$row = $entry->get_rundata('export');
						$row['dis'] = $dis->name;
						$row['cat'] = $cat->name;
						$row['count'] = 0;
						$export_table[$key] = $row;
						$sort[$key] = $this_sort;
					}
					$export_table[$key]['count']++ ;
				}
			}
		}
		if($sort) {
			array_multisort($sort, $export_table);
			foreach(array_keys($row) as $key) {
				$this->data['tfoot'][$key] = match($key) {
					'cat' => 'count',
					'count' => 'sum',
					default => ''
				};
			}
		}	
		# d($sort);
				
		$this->data['layout'] = 'table';
		break;
		
		case 'categories':
		$sort = [];
		foreach($this->data['entries'] as $dis) {
			foreach($dis->cats as $cat) {
				$export_table[] = [
					'dis' => $dis->name,
					'cat' => $cat->name,
					'count' => count($cat->entries)
				];
				$sort[] = "{$dis->name}-{$cat->sort}";
			}
		}
		if($sort) {
			array_multisort($sort, $export_table);
			foreach(array_keys($export_table[0]) as $key) {
				$this->data['tfoot'][$key] = match($key) {
					'cat' => 'count',
					'count' => 'sum',
					default => ''
				};
			}
		}
		$this->data['layout'] = 'table';
		break;
				
		default:
		$source = 'scoreboard';
		if($teamgym) {
			$rundata = tt_lib::get_rundata();
			# d($rundata);
		}
	
		$sort = [];
		$row = []; $run = [];
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
						'dob' => $entry->dob,
						'guest' => $entry->guest,
					];
					$row['order'] = '';#$entry->get_rundata('order');
					if($teamgym) {
						$run = $rundata[$entry->num] ?? $rundata[0] ;
						$row['run'] = $run;
						$sort[] = $run;
					}
					else {
						$row['run'] = $entry->get_rundata('export');
						// NB: same sort as running_order
						$sort[] = [
							$entry->get_rundata('order'),
							$dis->abbr,
							$cat->sort,
							$entry->num
						];						
					}
					$export_table[] = $row;
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

	$this->data['export'] = [];
	foreach($export_table as $row) {
		$row = array_flatten_with_dots($row);
		$tr = [];
		foreach($row as $key=>$val) {
			$key = str_replace('.', '_', $key);
			$tr[$key] = $val;
		}
		$this->data['export'][] = $tr;
	}
	
	// look for export request
	$filetype = $this->request->getGet('download');
	$this->data['filetypes'] = [
		'csv' => "download", // only using CSV
		# 'json' => "braces",
		# 'xml' => "code"
	];
	if(isset($this->data['filetypes'][$filetype])) {
		return $this->download($this->data, $this->data['layout'], $source, $filetype);
	}
	
	// view
	$this->data['source'] = $source;
	$this->data['heading'] .= ' - ' . humanize($source);
	$this->data['breadcrumbs'][] = ["admin/entries/export/{$event_id}", 'export'];

	// valid sources
	$arr = ['scoreboard', 'score_table', 'running_order', 'round_summary', 'entries', 'categories', 'entry_list'];
	$this->data['source_opts'] = [];
	foreach($arr as $key) {
		$this->data['source_opts'][$key] = humanize($key);
	}

	return view('entries/export', $this->data);			
}

}
 