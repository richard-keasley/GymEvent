<?php namespace App\Controllers\Setup;

class Logs extends \App\Controllers\BaseController {

public function __construct() {
	$this->data['breadcrumbs'][] = 'admin';
	$this->data['breadcrumbs'][] = 'setup';
	$this->data['breadcrumbs'][] = ['setup/logs', 'Error logs'];
	$this->get_files();
}

private function get_files() {
	$this->data['tempfiles'] = new \CodeIgniter\Files\FileCollection();
	
	// PHP native error log
	$error_log = realpath(ini_get('error_log'));
	if(is_file($error_log)) {
		$this->data['tempfiles']->addFile($error_log);
	}
	
	// CI logs
	$path = realpath(WRITEPATH . '/logs');
	if($path) {
		$this->data['tempfiles']->addDirectory($path);
		$this->data['tempfiles']->removePattern('index.*');
	}
}

private function findlog($logkey) {
	if(is_null($logkey)) return null;

	foreach($this->data['tempfiles'] as $key=>$file) {
		if($key==$logkey) return $file;
	}
	return null;	
}

public function index() {
	if($this->request->getPost('cmd')=='delete') {
		$logkey = $this->request->getPost('logkey');
		$logfile = $this->findlog($logkey);
		if($logfile) {
			$filepath = $logfile->getRealPath();
			$basename = $logfile->getBasename();
			if(unlink($filepath)) {
				$this->data['messages'][] = ["{$basename} deleted", 'info'];
				$this->get_files();
			}
			else {
				$this->data['messages'][] = ["Error deleting {$basename}", 'danger'];
			}		
		}
		else {
			$this->data['messages'][] = ["Can't find log file {$logkey}", 'danger'];
		}
	}

	if(!count($this->data['tempfiles'])) $this->data['messages'][] = ["No log files found", 'light'];

	// view
	$this->data['title'] = 'Error logs';
	$this->data['heading'] = $this->data['title'];
	return view('admin/setup/logs/index', $this->data);
}

public function view($logkey=0) {
	$logfile = $this->findlog($logkey);
	if(!$logfile) {
		$message = "Can't find log file {$logkey}";
		throw \App\Exceptions\Exception::not_found($message);
	}

	// view
	$this->data['buttons'] = [\App\Libraries\View::back_link("setup/logs")];
	if($logkey > 0) {
		$href = sprintf("/setup/logs/view/%s", $logkey - 1);
		$label = '<i class="bi-arrow-left"></i>';
		$attr = [
			'class' => "btn btn-outline-dark ",
			'title' => "Previous"
		];
		$this->data['buttons'][] = anchor($href, $label, $attr);
	}

	if($logkey < count($this->data['tempfiles'])) {
		$href = sprintf("/setup/logs/view/%s", $logkey + 1);
		$label = '<i class="bi-arrow-right"></i>';
		$attr = [
			'class' => "btn btn-outline-dark",
			'title' => "Next"
		];
		$this->data['buttons'][] = anchor($href, $label, $attr);
	}
	$this->data['buttons'][] = '<button class="btn btn-danger" type="submit"><i class="bi-trash"></i></button>';
		
	$this->data['title'] = "Error log {$logkey}";
	$this->data['heading'] = $this->data['title'];
	$this->data['breadcrumbs'][] = ["setup/logs/view/{$logkey}", $logkey];
	$this->data['logfile'] = $logfile;
	$this->data['logkey'] = $logkey;

	return view('admin/setup/logs/view', $this->data);
}

}
