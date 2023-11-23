<?php namespace App\Controllers;

class Follow extends \App\Controllers\BaseController {

public function index() {
	// ToDo: make this editable from UI
	$link = 'https://judging.k78design.co.uk/kiosk#/entry-info';
	return redirect()->to($link);
}

}
