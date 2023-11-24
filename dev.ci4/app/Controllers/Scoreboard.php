<?php namespace App\Controllers;

class Scoreboard extends \App\Controllers\BaseController {
	
public function __construct() {
	$this->data['breadcrumbs'][] = 'scoreboard';
	$appvars = new \App\Models\Appvars();
	$links = [
		['/scoreboard/displays', 'Score Display'],
		['/scoreboard/follow', 'Follow scores']
	];
	$this->data['links'] = new \App\Views\Htm\Navbar($links);
}

public function index() {
	$this->data['title'] = 'Scoreboard';
	$this->data['heading'] = 'Scoreboard';
	return view('scoreboard/index', $this->data);
}

public function displays() {
	$this->data['breadcrumbs'][] = 'scoreboard/displays';
	$this->data['title'] = 'Scoreboard';
	$this->data['heading'] = 'Scoreboard displays';
	return view('scoreboard/displays', $this->data);
}

public function follow($layout='') {
	$view = 'follow';
	$allowed = ['kiosk'];
	if(in_array($layout, $allowed)) $view .= "_{$layout}";
	
		
	$this->data['breadcrumbs'][] = 'scoreboard/follow';
	$this->data['title'] = 'Follow scores';
	$this->data['heading'] = '<span class="display-1">Follow scores</span>';
	return view("scoreboard/{$view}", $this->data);
}

}
