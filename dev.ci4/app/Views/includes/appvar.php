<?php
if(is_array($appvar->value)) {
	$tbody = [];
	foreach($appvar->value as $row_key=>$row_val) {
		$tr = ['_row' => sprintf('<span class="text-muted">%s</span>', $row_key)];
		if(!is_array($row_val)) {
			$row_val = ['value' => $row_val];
		}
		foreach($row_val as $col_key=>$col_val) {
			if(is_array($col_val)) $col_val = var_export($col_val, 10);
			$tr[$col_key] = $col_val;
		}
		$tbody[] = $tr;
	}
}
else $tbody = [$appvar->value]; 
	
if(count($tbody)) {
	$table = new \CodeIgniter\View\Table();
	$table->setTemplate(['table_open' => '<table class="table table-sm table-bordered">']);
	$headings = array_keys($tbody[0]);
	$headings[0] = '';
	$table->setHeading($headings);
	echo $table->generate($tbody);
}
