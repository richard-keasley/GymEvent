<?php namespace App\Libraries\General;

class Specials {
const blank = [
	'id' => 0,
	'level' => '',
	'gender' => '',
	'description' => '&nbsp;',
	'group' => '',
	'difficulty' => '',
	'hold' => false,
	'flexibility' => false,
	'strength' => false,
	'fs' => false,
	'afs' => false,
	'type' => ''
];

public $list = [];

function __construct($category) {
	$appvars = new \App\Models\Appvars();
	$arr = array_pad(explode('.', $category), 3, '');
	$appval = $appvars->get_value("general.{$arr[0]}.specials");
	if($appval) {
		foreach($appval as $id=>$special) {
			if($special['level']==$arr[1]) {
				if($special['gender']==$arr[2] || !$special['gender']) {
					$this->list[$id] = $special;
				}
			}
		}
	}
}

function options() {
	$retval = [self::blank['description']];
	foreach($this->list as $id=>$special) {
		$retval[$id] = $special['description'];
	}
	return $retval;
}

function get($id) {
	return isset($this->list[$id]) ? $this->list[$id] : self::blank ;
}

}