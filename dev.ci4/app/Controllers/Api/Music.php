<?php namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;

class Music extends \App\Controllers\BaseController {
	
use ResponseTrait;

private function getTrack() {
	$segments = $this->request->getUri()->getSegments();
	$track = new \App\Libraries\Track();
	$track->event_id = $segments[3] ?? 0 ;
	$track->entry_num = $segments[4] ?? 0 ;
	$track->exe = $segments[5] ?? '' ;
	return $track;
}
	
public function index() {
	return $this->respondNoContent();
}

public function track_url($event_id=0, $entry_num=0, $exe='') {
	$track = $this->getTrack();
	$url = $track->url();
	$response = [
		'status' => $url ? 'ok' : 'error',
		'message' => $url ? $url : "No music found for {$entry_num} {$exe}"
	];
	return $this->respond($response);
}

// send SSE from track_url, state
public function ssetrack() {
	if(!\App\Libraries\Auth::check_role('controller')) {
		return $this->failUnauthorized('Permission denied');
	}
	
	$state = $this->request->getPost('state');
	if($state=='play') {
		$filename = $this->request->getPost('url');
		if(!$filename) return $this->failNotFound('Track not found');
		
		$filename = parse_url($filename, PHP_URL_PATH);
		if(!$filename) return $this->failNotFound('path not found');
		
		$filename = pathinfo($filename, PATHINFO_BASENAME);
		if(!$filename) return $this->failNotFound('filename not found');
	}
	else {
		$state = 'pause';
		$filename = '';
	}
			
	$stream = new \App\Libraries\Sse\Stream('music');
	$event = $stream->channel->read();
	
	$id = $event->id ?? 0 ;
	$id++;
	
	$ssedata = [
		'id' => $id,
		'event' => $state,
		'data' => $state=='play' ? $filename : '',
	];
	$event = new \App\Libraries\Sse\Event($ssedata);
	$success = $stream->channel->write($event);
	if($success) {
		$event = $stream->channel->read();
		$response = $event->__toArray();
	} 
	else {
		$response = [
			'event'=>'error', 
			'data'=>'API error'
		];
	}
	return $this->respond($response);
}

// send SSE from state, event_id, entry_num, exe
public function sse() {
	if(!\App\Libraries\Auth::check_role('controller')) {
		return $this->failUnauthorized('Permission denied');
	}
	
	$stream = new \App\Libraries\Sse\Stream('music');
	$event = $stream->channel->read();
	
	$id = $event->id ?? 0 ;
	$id++;
		
	$ssedata = [
		'id' => $id,
		'event' => $this->request->getPost('state'),
		'data' => '',
	];
	$response = [
		'state' => $ssedata['event'],
		'label' => ''
	];
	
	if($ssedata['event']=='play') {
		// find track for this entry
		$track = new \App\Libraries\Track;
		$track->event_id = $this->request->getPost('event');
		$track->entry_num = $this->request->getPost('num');
		$track->exe = $this->request->getPost('exe');	
		$file = $track->file();
		if($file) {
			$ssedata['data'] = $file->getFilename();
			$response['label'] = $ssedata['data'];
		}
		else {
			$label = explode('_', $track->filebase());
			$label[0] = intval($label[0]);
			$response = [
				'state' => 'error', 
				'label' => sprintf('%s - not found', implode(' ', $label))
			];
			$ssedata['event'] = 'pause';
			$ssedata['data'] = $response['label'];
		}
	}
	$event = new \App\Libraries\Sse\Event($ssedata);
	$success = $stream->channel->write($event);
	if(!$success) {
		$response = ['state'=>'error', 'label'=>'API error'];
	}
	
	return $this->respond($response);
}

public function usertracks($user_id=0, $ent_id=0) {
if(!$user_id) $user_id = session('user_id');
$params = [
	'user_id' => intval($user_id),
	'ent_id' => intval($ent_id),	
];
# d($params);
	
$sql = "SELECT
`events`.`id` AS 'event_id',
`events`.`date` AS 'event_date',
`events`.title AS 'event_title',
`evt_entries`.`name` AS 'entry_name',
`evt_entries`.`num` AS 'entry_num',
`evt_entries`.`music` AS 'exe'

FROM `evt_entries` 
INNER JOIN `evt_categories` ON `evt_entries`.`category_id` = `evt_categories`.`id`
INNER JOIN `evt_disciplines` ON `evt_categories`.`discipline_id` = `evt_disciplines`.`id`
INNER JOIN `events` ON `evt_disciplines`.`event_id` = `events`.`id`

WHERE `evt_entries`.`user_id` = :user_id:
	AND `evt_entries`.`id` <> :ent_id:
	AND LENGTH(`evt_entries`.`music`) > 6

ORDER BY `events`.`date`, `evt_entries`.`name`";

$db = db_connect();
$res = $db->query($sql, $params);
# d((string) $db->getLastQuery());
# d($res->getResultArray());

$tbody = [];
$track = new \App\Libraries\Track;
foreach($res->getResultArray() as $key=>$row) {
	$track->event_id = $row['event_id'];
	$track->entry_num = $row['entry_num'];
	
	$exes = json_decode($row['exe'], 1);
	foreach($exes as $exe=>$state) {
		$track->exe = $exe;
		if(!$track->file()) continue;
		$row['exe'] = $track->exe;
		$tbody[] = $row;
		# d($track->event_id, $track->entry_num, $exe, $track->url());
	}
}

return $this->respond($tbody);
}

}
