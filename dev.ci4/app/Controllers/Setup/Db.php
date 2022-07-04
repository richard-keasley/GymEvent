<?php namespace App\Controllers\Setup;

class Db extends \App\Controllers\BaseController {
	
public function index() {
	$this->data['title'] = 'Database structure';
	$this->data['heading'] = $this->data['title'];
	$this->data['breadcrumbs'][] = 'admin';
	$this->data['breadcrumbs'][] = 'setup';
	$this->data['breadcrumbs'][] = ['setup/dev', 'development'];
	$this->data['breadcrumbs'][] = ['setup/dev', $this->data['title']];
	return view('admin/setup/db', $this->data);
}

}
