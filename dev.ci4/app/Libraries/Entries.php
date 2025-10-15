<?php namespace App\Libraries;

class Entries  {
	
private $attrs = [];	
function __construct($event_id) {
	$entries = model('Entries')->evt_discats($event_id, 1);
	$users = [];
	foreach($entries as $dis) { 
		foreach($dis->cats as $cat) { 
			foreach($cat->entries as $entry) {
				$user_id = $entry->user_id;
				if($user_id && empty($users[$user_id])) {
					$users[$user_id] = model('Users')->withDeleted()->find($user_id);				
				}
			}	
		}
	}
	$this->attrs = [
		'entries' => $entries,
		'users' => $users,
	];
}

function __get($key) {
	return $this->attrs[$key] ?? null;
}

function export($format) {
	$retval = [];
	switch($format) {
		case 'running_order':
		$sort = [];
		foreach($this->entries as $dis) {
			foreach($dis->cats as $cat) {
				foreach($cat->entries as $rowkey=>$entry) {
					$row = [
						'runorder' => implode(', ', $entry->get_rundata('runorder')),
						'dis' => $dis->name,
						'cat' => $cat->name,
						'num' => $entry->num,
						'club' => $this->users[$entry->user_id]->abbr ?? '?',
						'name' => $entry->name
					];
					if($entry->guest) $row['name'] .= ' (G)';
					
					if(!$rowkey) $has_opt = $entry->opt;
					if($has_opt) $row['opt'] = humanize($entry->opt);
					$retval[] = $row;
					
					$sort[] = [
						$entry->get_rundata('order'),
						$dis->abbr,
						$cat->sort,
						$entry->num
					];
				}
			}
		}
		array_multisort($sort, $retval);
		break;
		
		default:
		return null;
	}
	return $retval;
}

}