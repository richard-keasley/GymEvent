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

public function test($test_name='index', $param='') {
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
	
	if($test_name=='download') {
		$this->data['filename'] = $this->request->getGet('filename') ?? '' ;
		$this->data['layout'] = $this->request->getGet('layout') ?? '' ;
		$data = [
			[ 'dis' => 'MAG', 'cat' => 'snr', 'name' => 'John Doe', 'dob' => "12-May-2020" ],
			[ 'dis' => 'MAG', 'cat' => 'snr', 'name' => 'Fred "Jones"', 'dob' => "12-May-2020" ],
			[ 'dis' => 'MAG', 'cat' => 'jnr', 'name' => "Zoë O`hanlon", 'dob' => "12-May-2019" ],
			[ 'dis' => 'MAG', 'cat' => 'jnr', 'name' => "Chloë O'Donnel", 'dob' => "12-May-2019" ],
			[ 'dis' => 'WAG', 'cat' => 'snr', 'name' => "18° and more", 'dob' => "12-May-2010" ],
			[ 'dis' => 'WAG', 'cat' => 'snr', 'name' => "=[0,-1]", 'dob' => "12-May-2014" ],
			[ 'dis' => 'WAG', 'cat' => 'jnr', 'name' => "Fred&#8209;rick", 'dob' => "12-May-2000" ],
		];
		
		if($this->data['filename']) {
			switch($this->data['layout']) {
				case 'cattable':
				$cattable = new \App\Views\Htm\Cattable($data, ['dis', 'cat']);
				$cattable->table_header = 1;
				$data = $cattable;
				break;
			};
			return $this->download($this->data['filename'], $data);
		}
		$this->data['data'] = $data;
	}
	
	$this->data['param'] = $param;	
	$this->data['heading'] = $this->data['title'];	
	return view("{$test_path}/{$test_name}", $this->data);
}

public function whoops() {
	return view('errors/html/production');
}

}
