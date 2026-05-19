<?php namespace App\Controllers\Setup;

class Ipinfo extends \App\Controllers\BaseController {
	
public function index($ip=null) {
	$this->data['breadcrumbs'][] = 'admin';
	$this->data['breadcrumbs'][] = 'setup';
	$this->data['details'] = $ip;
	$this->data['heading'] = "IP info";
	return view('admin/setup/ipinfo', $this->data);
}

public function view($ip=null) {
	return $this->index($ip);
}

}
