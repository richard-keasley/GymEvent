<?php namespace App\Controllers;

class Links extends \App\Controllers\BaseController {

public function __construct() {
	$this->data['breadcrumbs'][] = 'links';
	
	$appvars = new \App\Models\Appvars();
	$links = $appvars->get_value('home.links');
	$this->data['links'] = $links;
}

public function index() {
	$this->data['title'] = 'links';
	return view('links/index', $this->data);
}

public function view($key='') {
	$link = $this->data['links'][$key] ?? null ;
	if($link) return redirect()->to($link);
	// link closed
	$message = "'{$key}' is not a valid link";
	throw \App\Exceptions\Exception::not_found($message);
}

}
