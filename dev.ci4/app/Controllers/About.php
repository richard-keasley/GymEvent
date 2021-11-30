<?php namespace App\Controllers;

class About extends \App\Controllers\BaseController {
	
public function __construct() {
	$this->data['breadcrumbs'][] = 'about';
}
	
public function index() {
	$this->data['title'] = 'About';
	$this->data['heading'] = 'About our service';
	return view('about/index', $this->data);
}

public function policy() {
	$this->data['breadcrumbs'][] = 'about/policy';
	$this->data['title'] = 'GDPR';
	$this->data['heading'] = 'Data Protection policy';
	return view('about/policy', $this->data);
}

}
