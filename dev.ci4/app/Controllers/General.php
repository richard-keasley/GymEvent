<?php namespace App\Controllers;

class General extends \App\Controllers\BaseController {

public function __construct() {
	$this->data['breadcrumbs'][] = ['general', 'General Gymnastics'];
	$this->data['title'] = "General Gymnastics";
	$this->data['heading'] = "General Gymnastics";
}
	
public function index() {
	return view('general/index', $this->data);
}

public function intention() {
	// collect POST
	$json = $this->request->getPost('routine');
		
	$this->data['intention'] = \App\Libraries\General\Intention::decode($json);
	if(!array_sum($this->data['intention']->skills)) {
		$this->data['messages'][] = ['Click the <span class="bi bi-question-circle"> help button</span> to learn how to start creating  your routine', 'info'];
	}

	// view
	$view = $this->request->getPost('view');
	$this->data['back_link'] = 'general';
	$this->data['title'] = $this->data['intention']->name;
	$this->data['heading'] = "Intention sheet - {$this->data['intention']->name}";
	$this->data['breadcrumbs'][] = ['general/intention', $this->data['heading']];
	
	if($view=='store') {
		$filename = preg_replace('#[^a-z_ ]#i', '', $this->data['intention']->name);
		$filename = str_replace(' ', '_', $filename); 
		$this->response->setHeader('Content-Disposition', sprintf('attachment; filename=%s-%s.html', $filename, $this->data['intention']->exercise));
		return view('general/intention/view', $this->data);
	}
	if($view=='print') {
		return view('general/intention/view', $this->data);
	}

	return view('general/intention/edit', $this->data);
} 
 
public function skills($exe='fx') {
	$exe = strtolower($exe);
	$this->data['skills'] = new \App\Libraries\General\Skills($exe);
	$this->data['back_link'] = 'general';
	$this->data['title'] = sprintf('%s skills', strtoupper($exe));
	$this->data['heading'] = $this->data['title'];
	$this->data['breadcrumbs'][] = ["general/skills/{$exe}", $this->data['heading']];
	return view('general/skills', $this->data);
}

} 
