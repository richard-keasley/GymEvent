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

public function data($varname='') {
	$this->data['breadcrumbs'][] = ['setup/scoreboard/data', 'data'];
	$this->data['scoreboard'] = new \App\ThirdParty\scoreboard;

	if($varname) {
		$file = $this->data['scoreboard']->get_include($varname);
		$db_ok = $this->data['scoreboard']->init_db();
		
		if($file && $db_ok) {
			$sql = "SELECT * FROM `{$varname}`";
			$this->data['tbody'] = $this->data['scoreboard']->query($sql);
			
			if($this->request->getPost('import')) {
				$import = [
					'date' => date('Y-m-d'),
					'table' => $this->data['tbody']
				];
				
				# $fileobj = $file->openFile('a');
				# $fileobj->fwrite("\n/*\n");
				# $fileobj->fwrite("<?php \n");
				# $fileobj->fwrite('$import = ' . var_export($import, 1));
				# $fileobj->fwrite("\n*/\n");
				$this->data['messages'][] = ['ToDo: data import', 'warning'];

			}
			
			
			$this->data['breadcrumbs'][] = "setup/scoreboard/data/{$varname}";
			$this->data['heading'] = "Scoreboard - {$varname}";
			$this->data['title'] = $varname;
		
			return view('scoreboard/var', $this->data);
		}

	
			# echo '<pre> $ret = ' . var_export($res, 1) . '; </pre>';
	}
	
	
	
	// view
	$this->data['heading'] = 'Scoreboard data';
	$this->data['title'] = 'Scoreboard data';
	return view('scoreboard/data', $this->data);
	
}

}
