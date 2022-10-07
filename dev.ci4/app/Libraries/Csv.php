<?php namespace App\Libraries;
/*
$csv = new \App\Libraries\Csv;
$csv->add_row($arr);
$csv->add_table($tbody, true);
$csv->write($filename);
*/

class Csv {
	
public $data = [];

function add_row($row) {
	foreach($row as $key=>$val) {
		$row[$key] =  html_entity_decode($val, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
	}	
	$this->data[] = $row;
}

function add_table($tbody, $header=0) {
	if(!$tbody) return;
	if($header) {
		$row = current($tbody);
		$this->add_row(array_keys($row));
	}
	foreach($tbody as $row) $this->add_row($row);
}

function write($filename) {
	if(!$this->data) return false;
	$fp = fopen($filename, 'w');
	if(!$fp) return false;
	foreach($this->data as $row) {
		fputcsv($fp, $row);
	}
	fclose($fp);
	return true;
}

}
