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
	
	$cellrow = 0;
	foreach($this->data as $rowkey=>$row) {
		$cellrow++;
		$cellcol = 0;
		
		foreach($row as $colkey=>$cell) {
			$cellcol++;
			
			if(preg_match('#^=.*\)$#i', $cell)) {
				$xlcell = new xlcell([$cellcol, $cellrow]);
				// extract parameter string
				preg_match('#\(.*\)#', $cell, $params);
				$params = $params[0] ?? '';
				$params = trim($params, '()');
				# echo $params;

				// get all cell coordinates
				preg_match_all('#\[[^\]]+\]#', $cell, $addrs);
				$addrs = $addrs[0] ?? [];
				// covert [x,y] to A1 
				foreach($addrs as $key=>$val) {
					$dxy = explode(',', trim($val, '[]'));
					$address = $xlcell->address($dxy);
					$row[$colkey] = str_replace($val, $address, $row[$colkey]);  
				}
			}
		}
					
		fputcsv($fp, $row);
	}
	fclose($fp);
	return true;
}

}

class xlcell implements \stringable{

private $xy = [];

function __construct($xy) {
	$this->xy = [
		intval($xy[0] ?? 1),
		intval($xy[1] ?? 1)
	];
}

function __toString() {
	return $this->address();
}

function address($dxy=[0,0]) {
	$xy = [
		$this->xy[0] + $dxy[0] ?? 0,
		$this->xy[1] + $dxy[1] ?? 0
	];
	return chr($xy[0] + 64) . $xy[1];
}

}
