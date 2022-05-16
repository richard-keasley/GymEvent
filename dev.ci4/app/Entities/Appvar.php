<?php namespace App\Entities;

use CodeIgniter\Entity;

class Appvar extends Entity {

protected $casts = [
	'value' => 'json-array'
];

public function htm() {
	$tbody = [];
	foreach($this->value as $row_key=>$row_val) {
		$tr = ['_row' => sprintf('<span class="text-muted">%s</span>', $row_key)];
		if(!is_array($row_val)) {
			$row_val = ['value' => $row_val];
		}
		foreach($row_val as $col_key=>$col_val) {
			if(is_array($col_val)) $col_val = var_export($col_val, true);
			$tr[$col_key] = $col_val;
		}
		$tbody[] = $tr;
	}
	if(!$tbody) return '';
	
	$template = ['table_open' => '<table class="table table-sm table-bordered">'];
	$table = new \CodeIgniter\View\Table($template);
	$headings = array_keys($tbody[0]);
	$headings[0] = '';
	$table->setHeading($headings);
	return $table->generate($tbody);
}

}
