<?php namespace App\Libraries\General;

class Composition {
const blank = [
	'id' => 0,
	'level' => '',
	'gender' => '',
	'A' => 0,
	'B' => 0,
	'C' => 0,
	'D' => 0,
	'E' => 0,
	'skill_count' => 10,
	'min_grp' => 0,
	'max_grp' => 10,
	1 => '',
	2 => '',
	3 => '',
	4 => '',
	'sr_val' => 1.0
];

static function load($category) {
	$retval = self::blank;
	$appvars = new \App\Models\Appvars();
	$arr = array_pad(explode('.', $category), 3, '');
	$appval = $appvars->get_value("general.{$arr[0]}.composition");
	if($appval) {
		foreach($appval as $row) {
			if($row['level']==$arr[1]) {
				if($row['gender']==$arr[2] || !$row['gender']) {
					$retval = $row;
				}
			}
		}
	}
	return $retval;
}

}