<?php namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;

class Music extends \App\Controllers\BaseController {
	
use ResponseTrait;
	
public function index() {
	return $this->respondNoContent();
}

public function track_url($event_id=0, $entry_num=0, $exe='', $local=false) {
	$track = new \App\Libraries\Track();
	$track->event_id = $event_id;
	$track->entry_num = $entry_num;
	$track->exe = $exe;
	$url = $track->url();
	if(!$url) return $this->failNotfound("No music found for {$entry_num} {$exe}");
	return $this->respond(base_url($url));
}

public function track($event_id=0, $entry_num=0, $exe='') {
	$track = new \App\Libraries\Track();
	$track->event_id = $event_id;
	$track->entry_num = $entry_num;
	$track->exe = $exe;
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

}
