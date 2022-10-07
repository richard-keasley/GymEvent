<?php namespace App\Controllers\Setup;

class Scoreboard extends \App\Controllers\BaseController {
	
public function __construct() {
	$this->data['title'] = 'Setup scoreboard';
	$this->data['breadcrumbs'][] = 'admin';
	$this->data['breadcrumbs'][] = 'setup';
	$this->data['breadcrumbs'][] = ['setup/scoreboard', $this->data['title']];
}
	
public function index() {
	$appvars = new \App\Models\Appvars();
	$var_name = 'scoreboard.links';

	if($this->request->getPost('save')) {
		$links = [];
		$post = $this->request->getPost('links');
		$post = $post ? json_decode($post, 1): [] ;
		foreach($post as $link) {
			if(!empty($link[0]) && !empty($link[1])) {
				$links[] = $link;
			}
		}
		$appvar = new \App\Entities\Appvar;
		$appvar->id = $var_name;
		$appvar->value = $links;
		$appvars->save_var($appvar);
	}	
	
	// view
	$this->data['heading'] = $this->data['title'];
	$this->data['links'] = $appvars->get_value($var_name);
	return view('scoreboard/setup', $this->data);
}

public function data() {
	// view
	$this->data['heading'] = 'Scoreboard data';
	$this->data['title'] = 'Scoreboard data';
	$this->data['breadcrumbs'][] = ['setup/scoreboard/data', 'data'];
	return view('scoreboard/data', $this->data);
	
}

}
