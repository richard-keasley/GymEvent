<?php namespace App\Controllers\Setup;

class Dev extends \App\Controllers\BaseController {
	
public function __construct() {
	$this->data['breadcrumbs'][] = 'admin';
	$this->data['breadcrumbs'][] = 'setup';
	$this->data['breadcrumbs'][] = ['setup/dev', 'Development'];
}
	
public function index() {
	$this->data['title'] = 'Development notes';
	$this->data['heading'] = $this->data['title'];
	return view('admin/setup/dev', $this->data);
}

public function test($test_name='index') {
	$this->data['breadcrumbs'][] = ['setup/dev/test', 'Test'];
	
	$test_path = 'admin/setup/test';
	$view_path = realpath(config('Paths')->viewDirectory); 
	$view_file = "{$view_path}/{$test_path}/{$test_name}.php";
	if(!is_file($view_file)) {
		$test_name = 'index';
		$view_file = "{$view_path}/{$test_path}/{$test_name}.php";
	}
	
	if($test_name=='index') {
		$this->data['title'] = 'Test pages';
	}
	else {
		$this->data['title'] = $test_name;
		$this->data['breadcrumbs'][] = ["setup/dev/test/{$test_name}", $test_name];
	}
	
	$this->data['postvars'] = $this->request->getPost();
	$this->data['heading'] = $this->data['title'];	
	return view("{$test_path}/{$test_name}", $this->data);
}

public function woops() {
	return view('errors/html/production');
}

}
