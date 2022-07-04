<?php namespace App\Controllers\Setup;

class Php_info extends \App\Controllers\BaseController {
	
public function index() {
	$this->data['breadcrumbs'][] = 'admin';
	$this->data['breadcrumbs'][] = 'setup';
	$this->data['breadcrumbs'][] = 'setup/php_info';
	$this->data['heading'] = 'PHP info';
	return view('admin/setup/php_info', $this->data);
}

}
