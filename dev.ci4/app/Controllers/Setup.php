<?php namespace App\Controllers;

class Setup extends \App\Controllers\BaseController {

public function __construct() {
	$this->data['breadcrumbs'][] = 'admin';
	$this->data['breadcrumbs'][] = 'setup';
}
	
public function index() {
	$controllers = [];
	$locked_controllers = ['setup','api'];
	$files = glob(APPPATH . "/Controllers/*");
	foreach($files as $file) {
		$controller = basename(strtolower($file), '.php');
		if(!in_array($controller, ['basecontroller', 'home'])) {
			$disabled = in_array($controller, \App\Libraries\Auth::$disabled);
			$controllers[$controller] = !$disabled;
		}		
	}
	
	if($this->request->getPost('save')) {
		$disabled = [];
		foreach(array_keys($controllers) as $controller) {
			$enabled = in_array($controller, $locked_controllers) ?
				1 :
				intval($this->request->getPost("chk_{$controller}"));
			if(!$enabled) $disabled[] = $controller;
			$controllers[$controller] = $enabled;
		}
		// update
		$data = [
			'id' => 'home.disabled',
			'value' => $disabled
		];
		$appvar = new \App\Entities\Appvar($data);
		$appvars = new \App\Models\Appvars();
		if($appvars->save_var($appvar)) {
			$this->data['messages'][] = ['saved info', 'success'];
			\App\Libraries\Auth::init();
		}
		else $this->data['messages'] = $appvars->errors();
	}

	// view
	$this->data['heading'] = 'System setup';
	$this->data['controllers'] = $controllers;
	$this->data['locked_controllers'] = $locked_controllers;
	return view('admin/setup/index', $this->data);
}

public function php_info() {
	$this->data['heading'] = 'PHP info';
	$this->data['breadcrumbs'][] = 'setup/php_info';
	return view('admin/setup/php_info', $this->data);
}

public function appvars() {
	$this->data['title'] = 'App variables';
	$this->data['heading'] = $this->data['title'];
	$this->data['breadcrumbs'][] = ['setup/appvars', $this->data['title']];
	return view('admin/setup/appvars', $this->data);
}

public function dev() {
	$this->data['title'] = 'Development notes';
	$this->data['heading'] = $this->data['title'];
	$this->data['breadcrumbs'][] = ['setup/dev', $this->data['title']];
	return view('admin/setup/dev', $this->data);
}

public function db() {
	$this->data['title'] = 'Database structure';
	$this->data['heading'] = $this->data['title'];
	$this->data['breadcrumbs'][] = ['setup/dev', 'development'];
	$this->data['breadcrumbs'][] = ['setup/dev', $this->data['title']];
	return view('admin/setup/db', $this->data);
}

public function update() {
	/* [source, dest, paths] */
	$this->data['datasets'] = [
	[	'source' => rtrim(ROOTPATH, DIRECTORY_SEPARATOR), 
		'dest' => dirname(ROOTPATH) . '/public.ci4',
		'paths' => ['/app', '/system']
	],
	[	'source' => rtrim(FCPATH, DIRECTORY_SEPARATOR), 
		'dest' => dirname(ROOTPATH) . '/public_html',
		'paths' => ['/app']
	]
	];
	
	$commit = $this->request->getPost('cmd')=='update';
	// update
	foreach($this->data['datasets'] as $datakey=>$dataset) {		
		$update = new \App\Libraries\Synchdirs($dataset['source'], $dataset['dest']);
		if($commit) $update->run($dataset['paths'], 1);
		# $update->verbose = 1;
		$this->data['datasets'][$datakey]['log'] = $update->run($dataset['paths']);
	}
	if($commit) $this->data['messages'][] = ['Live site updates applied', 'success'];
	// view	
	$this->data['title'] = 'App update state';
	$this->data['heading'] = $this->data['title'];
	$this->data['breadcrumbs'][] = ['setup/update', $this->data['title']];
	return view('admin/setup/update', $this->data);
}

public function install() {
	$this->data['title'] = 'Installation notes';
	$this->data['heading'] = $this->data['title'];
	$this->data['breadcrumbs'][] = ['setup/install', $this->data['title']];
	return view('admin/setup/install', $this->data);
}

public function scoreboard() {
	$appvars = new \App\Models\Appvars();
	$var_name = 'scoreboard.links';

	if($this->request->getPost('save')) {
		$links = [];
		$post = $this->request->getPost('links');
		$post = $post ? json_decode($post, 1): [] ;
		foreach($post as $link) {
			if(!empty($link['url']) && !empty($link['label'])) {
				$links[] = $link;
			}
		}
		$appvar = new \App\Entities\Appvar;
		$appvar->id = $var_name;
		$appvar->value = $links;
		$appvars->save_var($appvar);
	}	

	$this->data['links'] = $appvars->get_value($var_name);
	$this->data['title'] = 'Setup scoreboard';
	$this->data['heading'] = $this->data['title'];
	$this->data['breadcrumbs'][] = ['setup/scoreboard', $this->data['title']];
	return view('admin/setup/scoreboard', $this->data);
}

}
