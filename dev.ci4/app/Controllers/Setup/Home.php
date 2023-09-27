<?php namespace App\Controllers\Setup;

class Home extends \App\Controllers\BaseController {
	
public function index() {
	$this->data['breadcrumbs'][] = 'admin';
	$this->data['breadcrumbs'][] = 'setup';
	
	$controllers = [];
	$locked_controllers = ['setup', 'basecontroller', 'home'];
	$files = glob(APPPATH . "/Controllers/*.php");
	foreach($files as $file) {
		$controller = basename(strtolower($file), '.php');
		if(!in_array($controller, $locked_controllers)) {
			$disabled = in_array($controller, \App\Libraries\Auth::$disabled);
			$controllers[$controller] = !$disabled;
		}		
	}
	
	if($this->request->getPost('save')) {
		$disabled = [];
		foreach(array_keys($controllers) as $controller) {
			$enabled = intval($this->request->getPost("chk_{$controller}"));
			if(!$enabled) $disabled[] = $controller;
			$controllers[$controller] = $enabled;
		}
		// update
		$errors = null;
		$appvars = new \App\Models\Appvars();
		if(!$errors) {
			// min role
			$value = [
				'min' => $this->request->getPost('min_role')
			];
			$data = [
				'id' => 'home.roles',
				'value' => $value
			];
			$appvar = new \App\Entities\Appvar($data);
			if(!$appvars->save_var($appvar)) $errors = $appvars->errors();
		}
		if(!$errors) {
			// disabled controllers
			$data = [
				'id' => 'home.disabled',
				'value' => $disabled
			];
			$appvar = new \App\Entities\Appvar($data);
			if(!$appvars->save_var($appvar)) $errors = $appvars->errors();
		}
		if($errors) {
			$this->data['messages'] = $errors;
		}
		else {
			$this->data['messages'][] = ['saved info', 'success'];
			\App\Libraries\Auth::init();
		}
	}

	// view
	$this->data['heading'] = "System setup - {$this->data['device']}";
	$this->data['controllers'] = $controllers;
	$this->data['locked_controllers'] = $locked_controllers;
	return view('admin/setup/index', $this->data);
}

}
