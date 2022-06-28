<?php namespace App\Controllers\Admin;

class Home extends \App\Controllers\BaseController {
	
public function index() {
	$min_role = \App\Libraries\Auth::$min_role;
	if($min_role!=\App\Libraries\Auth::roles[0]) {
		$this->data['messages'][] = ["Minimum login role is '{$min_role}'", 'warning'];
	}
	$this->data['breadcrumbs'][] = 'admin';
	return view('admin/index', $this->data);
}

}
