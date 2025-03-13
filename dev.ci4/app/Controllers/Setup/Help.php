<?php namespace App\Controllers\Setup;

class Help extends \App\Controllers\BaseController {
	
public function __construct() {
	// look for help files
	$this->data['filebase'] = realpath(config('Paths')->viewDirectory . '/help');
	$files = new \CodeIgniter\Files\FileCollection([]);
	$files->addDirectory($this->data['filebase'], true);
	$files->retainPattern('*.php');
		
	$this->data['stubs'] = [];
	$start = strlen($this->data['filebase']) + 1;
	$length = -4; // length of .php extension 
	foreach($files as $key=>$file) {
		$this->data['stubs'][$key] = substr($file, $start, $length);
	}
		
	$this->data['breadcrumbs'][] = 'admin';
	$this->data['breadcrumbs'][] = 'setup';
	$this->data['breadcrumbs'][] = 'setup/help';
}
	
public function index() {
	$htmls = new \App\Models\Htmls;
		
	$del_item = $this->request->getPost('cmd')=='del_item';
	$item_id = (int) $this->request->getPost('item_id');
	if($del_item && $item_id) {
		$htmls->delete($item_id);
	}
	
	$new_path = $this->request->getPost('path');
	if($new_path) {
		$data = ['path' => $new_path];
		$new_id = $htmls->insert($data);
		if($new_id) {
			return redirect()->to("setup/help/edit/{$new_id}");
		}
		else {
			foreach($htmls->errors() as $msg) {
				$this->data['messages'][] = $msg;
			}
		}
	}
	
	return view('html/index', $this->data);
}

public function view($html_id=0) {
	$htmls = new \App\Models\Htmls;
	$this->data['html'] = $htmls->find($html_id);
	if(!$this->data['html']) {
		$message = "Can't find HTML entry {$html_id}";
		throw \App\Exceptions\Exception::not_found($message);
	}
	$this->data['breadcrumbs'][] = ["setup/help/view/{$html_id}", "#{$html_id}"];
	return view('html/view', $this->data);
}

public function edit($html_id=0) {
	$htmls = new \App\Models\Htmls;
	$this->data['html'] = $htmls->find($html_id);
	if(!$this->data['html']) {
		$message = "Can't find HTML entry {$html_id}";
		throw \App\Exceptions\Exception::not_found($message);
	}
	
	if($this->request->getPost('save')) {
		$keys = ['path', 'heading', 'value'];
		$update = ['id' => $html_id];
		foreach($keys as $key) $update[$key] = $this->request->getPost($key);
		
		if(!$htmls->save($update)) {
			foreach($htmls->errors() as $msg) {
				$this->data['messages'][] = $msg;
			}
		}
		$this->data['html'] = $htmls->find($html_id);
	}
	
	$this->data['modal_delete'] = [
		'action' => 'setup/help',
		'title' => "Delete this HTML entry",
		'description' => "<p>Are you sure you want to delete this entry?</p>",
		'item_id' => $html_id
	];
	
	$this->data['breadcrumbs'][] = ["setup/help/view/{$html_id}", "#{$html_id}"];
	$this->data['breadcrumbs'][] = ["setup/help/edit/{$html_id}", 'edit'];
	return view('html/edit', $this->data);
}

}
