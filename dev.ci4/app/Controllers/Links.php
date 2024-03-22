<?php namespace App\Controllers;

class Links extends \App\Controllers\BaseController {

public function __construct() {
	$this->data['breadcrumbs'][] = 'links';
	
	$appvars = new \App\Models\Appvars();
	$this->data['links'] = $appvars->get_value('home.links');
}

public function index() {
	return view('links/index', $this->data);
}

public function view($key='') {
	$link = $this->data['links'][$key] ?? null ;
	if($link==='') $link = base_url(); // link closed
	if($link) return redirect()->to($link);
	$message = "'{$key}' is not a valid link";
	throw \App\Exceptions\Exception::not_found($message);
}

}
