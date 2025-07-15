<?php namespace App\Controllers;

class About extends \App\Controllers\BaseController {
	
private $options = [
	'index' => [
		'title' => 'About us', 
		'html_path' => '~about-us',
	],
	'data' => [
		'title' => 'Data policy', 
		'html_path' => '~about-data'
	],
	'preparation' => [
		'title' => 'Preparations', 
		'html_path' => '~about-preparation'
	],
	'hardware' => [
		'title' => 'Hardware', 
		'html_path' => '~about-hardware'
	],
];
	
public function __construct() {
	$this->data['breadcrumbs'][] = 'about';
	
	$this->data['nav'] = '<nav class="nav">';
	foreach($this->options as $stub=>$option) {
		$url = $stub=='index' ? 
			'about' : 
			"about/{$stub}" ;
		$this->data['nav'] .= getlink($url, $option['title']);
		$this->options[$stub]['url'] = $url; 
	}
	$this->data['nav'] .= '</nav>';
}
	
public function index() {
	return $this->view('index');
}

public function view($stub='index') {
	$option = $this->options[$stub] ?? false ;
	# d($stub, $this->options, $option);
	if(!$option) {
		$message = "Can't find '{$stub}'";
		throw \App\Exceptions\Exception::not_found($message);
	}
	$html = model('Htmls')->find_path($option['html_path']);
	if(!$html) {
		$message = "Can't find '{$option['html_path']}'";
		throw \App\Exceptions\Exception::not_found($message);
	}	
	
	if($stub!='index') {
		$this->data['breadcrumbs'][] = [$option['url'], $option['title']];
	}
	
	foreach($option as $key=>$val) {
		$this->data[$key] = $val;
	}
	$this->data['heading'] = $html->heading ? $html->heading : $option['title'] ;
	$this->data['html'] = $html;
	$this->data['stub'] = $stub;
	
	return view('about/index', $this->data);
}

}
