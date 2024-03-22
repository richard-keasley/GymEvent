<?php namespace App\Controllers\Setup;

class Links extends \App\Controllers\BaseController {
	
public function __construct() {
	$this->data['title'] = 'Setup links';
	$this->data['breadcrumbs'][] = 'admin';
	$this->data['breadcrumbs'][] = 'setup';
	$this->data['breadcrumbs'][] = ['setup/links', $this->data['title']];
}
	
public function index() {
	$appvars = new \App\Models\Appvars();
	$var_name = 'home.links';
	
	// default values
	$links = [
		'follow' => "", 
		'scores' => "",
		'judges' => "",
		'info' => ""
	];
	
	// update
	if($this->request->getPost('save')) {
		foreach($links as $key=>$default) {
			$links[$key] = $this->request->getPost($key);
		}
		$appvar = new \App\Entities\Appvar;
		$appvar->id = $var_name;
		$appvar->value = $links;
		$appvars->save_var($appvar);
	}
	
	// read
	$var_value = $appvars->get_value($var_name);
	foreach($links as $key=>$default) {
		$links[$key] = $var_value[$key] ?? $default;
	}
	
	// view
	$this->data['heading'] = $this->data['title'];
	$this->data['links'] = $links;
	return view('links/setup', $this->data);
}

}
