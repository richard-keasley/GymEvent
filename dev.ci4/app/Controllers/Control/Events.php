<?php namespace App\Controllers\Control;

class Events extends \App\Controllers\BaseController {

private $mdl_events = null;

function __construct() {
	$this->data['breadcrumbs'][] = "events";
	$this->mdl_events = new \App\Models\Events;
}

private function find($event_id) {
	$event = $this->mdl_events->find($event_id);
	if(!$event) {
		$message = "Can't find event {$event_id}";
		throw \App\Exceptions\Exception::not_found($message);
	}
	$this->data['title'] = $event->title;
	$this->data['heading'] = $event->title;
	if($event->private) {
		$this->data['heading'] .= ' (private)';
	}
	return $event;
}

public function index() {
	return view('index', $this->data);
}

public function view($event_id=0) {
	$this->data['event'] = $this->find($event_id);
	
	$this->data['tables'] = [];
	
	if(in_array($this->data['event']->clubrets, [1, 2])) {
		// returns summary
		$fees = []; $cols = []; $count = []; $tbody = [];
		foreach($this->data['event']->clubrets() as $rowkey=>$clubret) {
			$user = $clubret->user;
			$label = $user ? $user->name : '[unknown]' ;

			$tbody[$rowkey] = [
				'club' => $user ? $user->name : '[unknown]',
				'email' => $user ? $user->email : '?',
				'updated' => $clubret->updated,
			];
			
			if($this->data['event']->stafffee) {
				$tbody[$rowkey]['staff'] = $clubret->stafffee;
			}

			if($this->data['event']->terms) {
				$tbody[$rowkey]['terms'] = $clubret->terms;
			}
					
			$count[$rowkey] = [];
			foreach($clubret->participants as $participant) {
				$dis = $participant['dis'];
				if(empty($count[$rowkey][$dis])) $count[$rowkey][$dis] = 0;
				$count[$rowkey][$dis]++;
				if(!in_array($dis, $cols)) $cols[] = $dis;
			}
			
			$cr_fees = $clubret->fees;
			$fees[$rowkey] = array_sum(array_column($cr_fees, 1));
		}

		foreach($tbody as $rowkey=>$row) {
			foreach($cols as $colkey) {
				$val = $count[$rowkey][$colkey] ?? 0;
				$tbody[$rowkey][$colkey] = $val;
			}
			$tbody[$rowkey]['fees'] = $fees[$rowkey];
		}
		$this->data['tables']['club_returns'] = $tbody;
	}
	
	if(in_array($this->data['event']->clubrets, [1, 2])) {
		// category summary 
		$tbody = []; 
		foreach($this->data['event']->participants() as $dis) {
			foreach($dis['cats'] as $cat) {
				$tbody[] = [
					'dis' => $dis['name'],
					'cat' => $cat['name'],
					'count' => count($cat['entries']),
				];
			}
		}
		$this->data['tables']['categories'] = $tbody;
	}
	
	if(in_array($this->data['event']->clubrets, [2])) {
		// participants detail 
		$tbody = []; $count = 1;
		foreach($this->data['event']->participants() as $dis) { 
			foreach($dis['cats'] as $cat) { 	 
				foreach($cat['entries'] as $entkey=>$entry) {
					if(!$entry['club']) $entry['club'] = 'unknown';
					$row = [
						'#' => $count++,
						'dis' => $dis['name'],
						'cat' => humanize($cat['name']),
						'club' => $entry['club'],
						'name' => $entry['name'],
						'DoB' => $entry['dob'],
					];
					if(!$entkey) $has_opt = $entry['opt'];
					if($has_opt) $row['opt'] = humanize($entry['opt']);
					$tbody[] = $row;
				}
			}
		}
		$this->data['tables']['participants'] = $tbody;
	}
	
	if(in_array($this->data['event']->clubrets, [2])) {
		// staff	
		$tbody = [];
		foreach($this->data['event']->staff() as $entkey=>$entry) {
			$tbody[] = [
				# '#' => $entkey + 1,
				'club' => $entry['club'],
				'role' => humanize($entry['cat']),
				'name' => $entry['name'],
				# 'BG' => $entry['bg'],
				# 'DoB' => date('d-M-Y', $entry['dob'])
			];
		}
		$this->data['tables']['staff'] = $tbody;
	}
	
	$dl = $this->request->getGet('dl');
	$export = $this->data['tables'][$dl] ?? null;
	if($export) {
		$filename = "{$this->data['event']->title}_{$dl}.csv";
		return $this->download($filename, $export);
	}
			
	return view('events/control', $this->data);
}

}
