<?php namespace App\Controllers\Setup;

class Dev extends \App\Controllers\BaseController {
	
public function index() {
	$this->data['title'] = 'Development notes';
	$this->data['heading'] = $this->data['title'];
	$this->data['breadcrumbs'][] = 'admin';
	$this->data['breadcrumbs'][] = 'setup';
	$this->data['breadcrumbs'][] = ['setup/dev', $this->data['title']];
	return view('admin/setup/dev', $this->data);
}

}
