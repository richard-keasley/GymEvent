<?php namespace App\Libraries\General;

class Bonuses {
const blank = [
	'id' => 0,
	'level' => '&nbsp;',
	'gender' => '&nbsp;',
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
	$appval = $appvars->get_value("general.{$arr[0]}.bonuses");
	if($appval) {
		foreach($appval as $id=>$bonus) {
			if($bonus['level']==$arr[1]) {
				if($bonus['gender']==$arr[2] || !$bonus['gender']) {
					$this->list[$id] = $bonus;
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