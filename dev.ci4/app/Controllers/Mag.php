<?php namespace App\Controllers;

class Mag extends \App\Controllers\BaseController {

public function __construct() {
	$this->data['breadcrumbs'][] = ['mag', "Men's Artistic"];
	$this->data['title'] = "Men's Artistic";
	$this->data['heading'] = "Men's Artistic";
}
	
public function index() {
	$this->data['index'] = \App\Libraries\Mag\Rules::index();
	return view('mag/index', $this->data);
}

public function rules($setname = null) {
	$this->data['ruleset'] = \App\Libraries\Mag\Rules::load($setname);
	if(!$this->data['ruleset']) {
		throw new \RuntimeException("Can't find MAG rule set $setname", 404);
	}
	$this->data['breadcrumbs'][] = ["mag/rules/{$setname}", $this->data['ruleset']->title];
	$this->data['title'] = $this->data['ruleset']->title;
	$this->data['heading'] = $this->data['ruleset']->title;
	return view('mag/rules', $this->data);
}

public function routine() {
	$getPost = $this->request->getPost();
	$this->data['exeset'] = new \App\Libraries\Mag\Exeset($getPost);
	$this->data['breadcrumbs'][] = ['mag/routine', "Routine sheet"];
	return view('mag/routine', $this->data);
}








public function intention() {
	// collect POST
	$json = $this->request->getPost('routine');
	
	/* test value */
	/*if(!$json) {
		$json = '{"name":"Jane Doe", "gender":"female", "dob":"2010-10-12", "exercise":"FX", "level":"gold", "skills":[10,116,78,143,87,16,86,140,76,141], "specials":[620,0,0,0,0,600,0,0,0,621], "bonuses":[0,0,0,0,0,0,0,62,61,0], "v_rules":"2018-02-01", "v_program":"2021-05-14", "v_updated":"2021-05-14"}';
	} 
	// */
	
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
