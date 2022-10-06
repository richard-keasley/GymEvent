<?php namespace App\Libraries;

class Csv {
private $fp = null;
private $filename = null; 

function open($filename) {
	$this->fp = fopen($filename, 'w');
	$this->filename = $this->fp ? $filename : null;
	return $this->fp;
}

function put_row($row) {
	foreach($row as $key=>$val) {
		$row[$key] =  html_entity_decode($val, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
	}	
	fputcsv($this->fp, $row);
}

function put_table($tbody, $header=0) {
	if(!$tbody) return;
	if($header) {
		$row = current($tbody);
		$this->put_row(array_keys($row));
	}
	foreach($tbody as $row) $this->put_row($row);
}

function close() {
	fclose($this->fp);
	$this->fp = null;
}

}
