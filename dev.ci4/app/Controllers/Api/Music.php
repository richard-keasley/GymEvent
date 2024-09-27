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
	return $url ? 
		$this->respond($url): 
		$this->failNotfound("No music found for {$entry_num} {$exe}");
}

public function _track($event_id=0, $entry_num=0, $exe='') {
	// ToDo: delete this function (4 June 2024)
	$track = $this->getTrack();
	$file = $track->file();
	if(!$file) return $this->failNotfound("No music found for {$entry_num} {$exe}");
	$filename = $track->filepath() . $file->getFilename();
	return $this->response->download($filename, null); 
}

public function set_remote() {
	if(!\App\Libraries\Auth::check_role('controller')) {
		return $this->failUnauthorized('Permission denied');
	}
	
	$getPost = []; $post_fail = 0;
	foreach(['event', 'entry', 'exe', 'state'] as $key) {
		$getPost[$key] = $this->request->getPost($key);
		if(is_null($getPost[$key])) $post_fail = 1;
	}
	if($post_fail) {
		return $this->fail('Incomplete post');
	}
		
	$track = new \App\Libraries\Track();
	$track->event_id = $getPost['event'];
	$track->entry_num = $getPost['entry'];
	$track->exe = $getPost['exe'];
	$getPost['url'] = $track->url();
	$getPost['label'] = $track->label();
		
	$appvars = new \App\Models\Appvars();
	$appvar = new \App\Entities\Appvar;
	$appvar->id = 'music.remote';
	$appvar->value = $getPost;
	$appvars->save_var($appvar);
	
	if($getPost['url']) {
		return $this->respond($appvar->value);
	}
	else {
		return $this->failNotfound("No music found for {$getPost['entry']} {$getPost['exe']}");
	}
}

public function auto($ch_id=0) {
	$response = '';
	switch(intval($ch_id)) {
		case 1:
			$appvars = new \App\Models\Appvars();
			$appvar = $appvars->find('music.remote');
			if(!$appvar) return $this->fail("Can't find music.remote");
			$response = $appvar->value;
			if($response['url']) $response['url'] = base_url($response['url']);
			$response['updated'] = $appvar->updated_at->toDateTimeString();
	}
	if($response) return $this->respond($response);
	else return $this->fail('error');
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
