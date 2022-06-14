<?php namespace App\Controllers\Admin;

class Help extends \App\Controllers\BaseController {
	
public function __construct() {
	// look for help files
	$this->data['filebase'] = realpath(config('Paths')->viewDirectory . '/help');
	$files = new \CodeIgniter\Files\FileCollection([]);
	$files->addDirectory($this->data['filebase'], true);
	$files->retainPattern('*.php');
		
	$this->data['stubs'] = [];
	$start = strlen($this->data['filebase']) + 1;
	$length = -4; // length of .php extension 
	foreach($files as $key=>$file) {
		$this->data['stubs'][$key] = substr($file, $start, $length);
	}
		
	$this->data['breadcrumbs'][] = 'admin';
	$this->data['breadcrumbs'][] = 'admin/help';
}
	
public function index() {
	return view('admin/help/index', $this->data);
}

public function view($key=0) {
	$key = intval($key);
	$viewname = $this->data['stubs'][$key];
	$include = $this->data['filebase'] . "/{$viewname}.php";
	$this->data['include'] = file_exists($include) ? $include : '';
	if(!$this->data['include']) $this->data['messages'][] = "No help file found";
	
	$this->data['breadcrumbs'][] = ["admin/help/view/{$key}", 'view'];
	return view('admin/help/view', $this->data);
}

}
