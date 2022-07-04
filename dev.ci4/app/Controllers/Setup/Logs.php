<?php namespace App\Controllers\Setup;

class Logs extends \App\Controllers\BaseController {
	
public function __construct() {
	$this->data['breadcrumbs'][] = 'admin';
	$this->data['breadcrumbs'][] = 'setup';
	$this->data['breadcrumbs'][] = ['setup/logs', 'Error logs'];
	
	$this->data['logfiles'] = new \CodeIgniter\Files\FileCollection();
	$path = realpath(WRITEPATH . '/logs');
	if($path) {
		$this->data['logfiles']->addDirectory($path);
		$this->data['logfiles']->removePattern('index.*');
	}
}

private function findlog($logkey) {
	$logfile = null;
	if(is_null($logkey)) return $logfile;
	foreach($this->data['logfiles'] as $key=>$file) {
		if($key==$logkey) $logfile = $file;
	}
	return $logfile;	
}
	
public function index() {
	if($this->request->getPost('cmd')=='delete') {
		$logkey = $this->request->getPost('logkey');
		$logfile = $this->findlog($logkey);
		if($logfile) {
			$message = sprintf('%s deleted', $logfile->getBasename());
			$this->data['messages'][] = [$message, 'info'];
		}
		else {
			$this->data['messages'][] = ["Can't find log file {$logkey}", 'danger'];
		}
	}
	
	// view
	$this->data['title'] = 'Error logs';
	$this->data['heading'] = $this->data['title'];
	return view('admin/setup/logs/index', $this->data);
}

public function view($logkey=0) {
	$logfile = $this->findlog($logkey);
	if(!$logfile) throw new \RuntimeException("Can't find log file {$logkey}", 404);

	// view
	$this->data['title'] = "Error log {$logkey}";
	$this->data['heading'] = $this->data['title'];
	$this->data['breadcrumbs'][] = ["setup/logs/view/{$logkey}", $logkey];
	$this->data['logfile'] = $logfile;
	$this->data['logkey'] = $logkey;

	return view('admin/setup/logs/view', $this->data);
}

}
