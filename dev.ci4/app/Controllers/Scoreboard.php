<?php namespace App\Controllers;

class Scoreboard extends \App\Controllers\BaseController {
	
public function index() {
	$this->data['breadcrumbs'][] = 'scoreboard';
	$this->data['title'] = 'Scoreboard';
	$this->data['heading'] = 'Score service';
	return view('scoreboard/index', $this->data);
}

}
