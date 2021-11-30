<?php namespace App\Controllers\Admin;

class Home extends \App\Controllers\BaseController {
	
public function index() {
	$this->data['breadcrumbs'][] = 'admin';
	return view('admin/index', $this->data);
}

}
