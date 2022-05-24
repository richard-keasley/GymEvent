<?php namespace App\Views\Htm;

class Cattable {
private $table = null; 

public $data = [];
public $headings = []; // data columns to convert to HTM headings
public $table_header = false; // include table header
public $template_name = 'bordered'; // table template

public function __construct($headings=[]) {
	$this->headings = $headings;
}

public function htm($data = false) {
	if(!$data) $data = $this->data;
	if(!$data) return;

	$this->table = \App\Views\Htm\Table::load($this->template_name);
	
	// HTM heading
	$headings = []; $formats = [];
	foreach($this->headings as $key=>$fldname) {
		$headings[$fldname] = '';
		$lvl = $key + 3;
		$formats[$fldname] = "<h{$lvl}>%s</h{$lvl}>";
	}

	// table header
	if($this->table_header) {
		$thead = [];
		foreach(array_keys(current($data)) as $fldname) {
			if(!isset($headings[$fldname])) {
				$thead[] = $fldname;
			}
		}
	}
	else $thead = false;
	
	ob_start();
	
	# d($this->headings);
	# d($thead);
	# d($data);
	
	echo '<section class="cattable">';
	$tbody = []; $this_row = [];
	$table_cats = ''; // HTM heading
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
			echo $this->generate($tbody, $thead);
			$tbody = [];
			echo $table_cats;
			$table_cats = '';
			
		}
		$tbody[] = $this_row;
	}
	
	echo $this->generate($tbody, $thead);
	echo '</section>';
	return ob_get_clean();
}

private function generate($tbody, $thead) {
	if(!$tbody) return '';
	
	if($thead) {
		$this->table->setHeading($thead);
	}
	else {
		$this->table->autoHeading = false;
	}
	return $this->table->generate($tbody);
}

}
