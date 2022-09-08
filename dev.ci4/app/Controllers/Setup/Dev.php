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

public function test() {
	$this->data['title'] = 'Test page';
	$this->data['heading'] = $this->data['title'];
	$this->data['breadcrumbs'][] = ['setup/dev/test', $this->data['title']];
	return view('admin/setup/test', $this->data);
}

}
