<?php namespace App\Controllers\Control;

class Home extends \App\Controllers\BaseController {
	
public function index() {
	return view('index', $this->data);
}

}
