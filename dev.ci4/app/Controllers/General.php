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
	$exe_title = strtoupper($exe);
	$this->data['skills'] = new \App\Libraries\General\Skills($exe);
	if(empty($this->data['skills']->list)) {
		$message = "Can't find exercise '{$exe_title}'";
		\App\Libraries\Exception::not_found($this->request, $message);
	}
	
	$this->data['back_link'] = 'general';
	$this->data['title'] = sprintf('%s skills', $exe_title);
	$this->data['heading'] = $this->data['title'];
	$this->data['breadcrumbs'][] = ["general/skills/{$exe}", $this->data['heading']];
	return view('general/skills', $this->data);
}

public function rules($exe='fx') {
	$exe = strtolower($exe);
	$exe_title = strtoupper($exe);
	
	$rule_parts = ['composition', 'specials', 'bonuses'];
	$appvars = new \App\Models\Appvars();
		
	$this->data['rules'] = [];
	foreach($rule_parts as $rule_part) {
		$appval = $appvars->get_value("general.{$exe}.{$rule_part}");
		if($appval) {
			foreach($appval as $key=>$row) {
				# unset($appval[$key]['id']);
				if(empty($row['gender'])) $appval[$key]['gender'] = '<span class="text-muted fst-italic">all</span>';
				foreach(['hold', 'flexibility', 'strength', 'fs', 'afs'] as $attr) {
					if(isset($row[$attr])) {
						$appval[$key][$attr] = $row[$attr] ? '<span class="text-ok bi-check"></span>' : '' ;
					}
				}
				foreach(['A', 'B', 'C', 'D', 'E'] as $attr) {
					if(isset($row[$attr])) {
						if(!$row[$attr]) $appval[$key][$attr] = '-';
					}
				}

			}
		}
		$this->data['rules'][$rule_part] = $appval ?? "'{$exe_title}/{$rule_part}' not found";
	}
		
	$this->data['back_link'] = 'general';
	$this->data['title'] = sprintf('%s rules', $exe_title);
	$this->data['heading'] = $this->data['title'];
	$this->data['breadcrumbs'][] = ["general/skills/{$exe}", $this->data['heading']];
	return view('general/rules', $this->data);
}

} 
