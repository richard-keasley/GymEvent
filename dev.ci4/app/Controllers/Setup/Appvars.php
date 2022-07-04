<?php namespace App\Controllers\Setup;

class Appvars extends \App\Controllers\BaseController {
	
public function index() {
	$this->data['title'] = 'App variables';
	$this->data['heading'] = $this->data['title'];
	$this->data['breadcrumbs'][] = 'admin';
	$this->data['breadcrumbs'][] = 'setup';
	$this->data['breadcrumbs'][] = ['setup/appvars', $this->data['title']];
	return view('admin/setup/appvars', $this->data);
}

}
