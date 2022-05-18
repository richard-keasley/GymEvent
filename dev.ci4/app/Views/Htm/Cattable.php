<?php namespace App\Views\Htm;

class Cattable {
public $data = [];
public $headings = [];

public function __construct($headings=[]) {
	$this->headings = $headings;
}

public function htm($data = false) {
	ob_start();

	$table = \App\Views\Htm\Table::load('bordered');

	if(!$data) $data = $this->data;
	if(!$data) return;
	
	d($this->headings);
	d($data);
	
	$tbody = []; $this_row = [];
			
	$headings = []; $formats = [];
	foreach($this->headings as $key=>$fldname) {
		$headings[$fldname] = '';
		$lvl = $key + 3;
		$formats[$fldname] = "<h{$lvl}>%s</h{$lvl}>";
	}
	
	/*
	$thead = [];
	foreach(array_keys(current($data)) as $fldname) {
		if(!isset($headings[$fldname])) {
			$thead[] = $fldname;
		}
	}
	*/
	
	$table_cats = '';
	foreach($data as $row) {
		foreach($row as $fldname=>$val) {
			if(isset($headings[$fldname])) {
				if($headings[$fldname]!==$val) {
					$headings[$fldname] = $val;
					$level = array_search($fldname, $this->headings) + 3;
					$table_cats .= sprintf($formats[$fldname], $val);
				}
				# $this_row[$fldname] = $val;
			}
			else {
				$this_row[$fldname] = $val;
			}
		}
		if($table_cats) {
			if($tbody) {
				# $table->setHeading($thead);
				$table->autoHeading = false;
				echo $table->generate($tbody);
			}
			$tbody = [];
			echo $table_cats;
			$table_cats = '';
			
		}
		$tbody[] = $this_row;
		
	}
	if($tbody) {
		# $table->setHeading($thead);
		$table->autoHeading = false;
		echo $table->generate($tbody);
	}
	
	return ob_get_clean();
}


}
