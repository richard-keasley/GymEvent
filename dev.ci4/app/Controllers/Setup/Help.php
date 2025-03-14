<?php namespace App\Controllers\Setup;

class Help extends \App\Controllers\BaseController {
	
public function __construct() {	
	$this->data['breadcrumbs'][] = 'admin';
	$this->data['breadcrumbs'][] = 'setup';
	$this->data['breadcrumbs'][] = 'setup/help';
}
	
public function index() {
	$htmls = new \App\Models\Htmls;

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
	
	$delsure = [
		'message' => 'delete this HTML item?',
	];
	$this->data['delsure'] = new \App\Views\Htm\Delsure($delsure);
	$del_id = $this->data['delsure']->request;
	if($del_id) {
		$htmls->delete($del_id);
		return redirect()->to("setup/help");
	}
	
	$this->data['breadcrumbs'][] = ["setup/help/view/{$html_id}", "#{$html_id}"];
	$this->data['breadcrumbs'][] = ["setup/help/edit/{$html_id}", 'edit'];
	return view('html/edit', $this->data);
}

}
