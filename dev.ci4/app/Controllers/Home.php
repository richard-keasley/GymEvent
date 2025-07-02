<?php namespace App\Controllers;

class Home extends \App\Controllers\BaseController {
	
public function index() {
	return view('index', $this->data);
}

public function js($filename='') {
	$this->response->setHeader('Content-Type', 'application/javascript');
	ob_start();
	$filename = realpath(config('Paths')->viewDirectory . "/js/{$filename}.js");
	if($filename) include $filename;
	return ob_get_clean();
}

public function pwa($mode=null) {
	$allowed = ['browser', 'standalone'];
	if(in_array($mode, $allowed)) {
		$_SESSION['pwa'] = $mode;
	}
	return redirect()->to(site_url());
}

}
