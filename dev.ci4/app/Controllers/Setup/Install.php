<?php namespace App\Controllers\Setup;

class Install extends \App\Controllers\BaseController {
	
public function index() {
	$this->data['title'] = 'Installation notes';
	$this->data['heading'] = $this->data['title'];
	$this->data['breadcrumbs'][] = 'admin';
	$this->data['breadcrumbs'][] = 'setup';
	$this->data['breadcrumbs'][] = ['setup/install', $this->data['title']];
	return view('admin/setup/install', $this->data);
}

}
