<?php namespace App\Controllers;

class Scoreboard extends \App\Controllers\BaseController {
	
public function __construct() {
	$this->data['breadcrumbs'][] = 'scoreboard';
}

public function index() {
	$path = '~scoreboard';
	$html = (new \App\Models\Htmls)->find_path($path);
	if(!$html) {
		$message = "Can't find '{$path}'";
		throw \App\Exceptions\Exception::not_found($message);
	}
	
	$this->data['title'] = 'Scoreboard';
	$this->data['html'] = $html;
	$this->data['heading'] = $html->heading;
	return view('scoreboard/index', $this->data);
}

public function follow($layout='') {
	$view = 'follow';
	$allowed = ['kiosk'];
	if(in_array($layout, $allowed)) $view .= "_{$layout}";
			
	$this->data['breadcrumbs'][] = 'scoreboard/follow';
	$this->data['title'] = 'Follow scores';
	$this->data['heading'] = '<span class="display-1">Follow scores</span>';
	$this->data['link'] = 'x/follow';
	return view("scoreboard/{$view}", $this->data);
}

public function info($layout='') {
	$view = 'info';
	$allowed = ['kiosk'];
	if(in_array($layout, $allowed)) $view .= "_{$layout}";
			
	$this->data['breadcrumbs'][] = 'scoreboard/info';
	$this->data['title'] = 'Event info';
	$this->data['heading'] = '<span class="display-1">Event information</span>';
	$this->data['link'] = 'x/info';
	return view("scoreboard/{$view}", $this->data);
}

}
