<?php namespace App\Controllers;

class Follow  extends \App\Controllers\BaseController {

public function index() {
	$follow = 'https://judging.k78design.co.uk/kiosk#/entry-info';
	return redirect()->to($follow);
}

}
