<?php namespace App\Libraries\General;

class Rules {
const version = '2018-02-01';
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