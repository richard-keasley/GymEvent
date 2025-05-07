<?php namespace App\Controllers\Setup;

class Update extends \App\Controllers\BaseController {
	
public function index() {
	/* [source, dest, paths] */
	$this->data['datasets'] = [
	[	'source' => rtrim(ROOTPATH, DIRECTORY_SEPARATOR), 
		'dest' => dirname(ROOTPATH) . '/public.ci4',
		'paths' => ['/app']
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
	$this->data['breadcrumbs'][] = 'admin';
	$this->data['breadcrumbs'][] = 'setup';
	$this->data['breadcrumbs'][] = ['setup/update', $this->data['title']];
	return view('admin/setup/update', $this->data);
}

}
