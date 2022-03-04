<?php namespace App\Controllers;

class About extends \App\Controllers\BaseController {
	
public function __construct() {
	$this->data['breadcrumbs'][] = 'about';
	$this->data['nav'] = 
		'<nav class="nav">' . 
		getlink('about', 'About us') .
		getlink('about/policy', 'Data Policy') .
		getlink('about/timeline', 'Preparations') .
		getlink('about/hardware', 'Hardware') .
		'</nav>';
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

public function timeline() {
	$this->data['breadcrumbs'][] = 'about/timeline';
	$this->data['title'] = 'Timeline';
	$this->data['heading'] = 'Timeline for preparations';
	return view('about/timeline', $this->data);
}

public function hardware() {
	$this->data['breadcrumbs'][] = 'about/hardware';
	$this->data['title'] = 'Hardware';
	$this->data['heading'] = 'Hardware requirements';
	return view('about/hardware', $this->data);
}

}
