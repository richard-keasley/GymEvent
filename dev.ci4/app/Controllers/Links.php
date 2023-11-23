<?php namespace App\Controllers;

class Links extends \App\Controllers\BaseController {
	
// ToDo: make this editable from UI
const links = [
	'follow' => "https://judging.k78design.co.uk/kiosk#/entry-info",
	'scores' => "https://judging.k78design.co.uk/display#/identify/{website}",
	'judges' => "https://judging.k78design.co.uk/",
];

public function __construct() {
	$this->data['breadcrumbs'][] = 'links';
	$this->data['links'] = self::links;
}

public function index() {
	return view('links/index', $this->data);
}

public function view($key='') {
	$link = $this->data['links'][$key] ?? null ;
	if($link) return redirect()->to($link);
		
	$message = "Invalid link: {$key}";
	throw \App\Exceptions\Exception::not_found($message);
}

}
