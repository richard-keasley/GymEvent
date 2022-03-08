<?php namespace App\Libraries\General;

if(empty(Rules::$version)) {
	$appvars = new \App\Models\Appvars();
	$appval = $appvars->get_value("general.fx.rules");
	Rules::$version = $appval['version'] ?? '';
}

class Rules {
static $version = null; // initialised above
public $category = '';
public $skills = [];
public $bonuses = [];
public $specials = [];

function __construct($category) {
	$this->category = $category;
	$this->composition = \App\Libraries\General\Composition::load($this->category);
	$this->skills = new \App\Libraries\General\Skills($this->category, $this->composition);
	$this->specials = new \App\Libraries\General\Specials($this->category);
	$this->bonuses = new \App\Libraries\General\Bonuses($this->category);
}
	
}
