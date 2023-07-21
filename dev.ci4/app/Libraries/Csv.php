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
	foreach($this->data as $rowkey=>$row) {
		$row_addr = $rowkey + 1;
		foreach($row as $colkey=>$cell) {
			if(preg_match('#^\{.*\}$#i', $cell)) {
				$func = null;
				$params = [];
				$arr = explode(' ', trim($cell, '{}'));
				foreach($arr as $key=>$val) {
					if(!$key) $func = trim($val);
					elseif(strlen($val)) {
						$params[] = intval($val);
					}
				}
				
				switch($func) {
					case 'sum':
					if(count($params)==2) {
						$range = [];
						foreach($params as $key=>$val) {
							$cell_addr = chr($val+64) . $row_addr;
							$range[] = $cell_addr;
						}
						$row[$colkey] = sprintf('=SUM(%s)', implode(':', $range));
					}
					break;
					
					case 'rank':
					if(count($params)==3) {
						$range = [];
						foreach($params as $key=>$val) {
							switch($key) {
								case 0:
								$col_addr = chr($val+64);
								$source = $col_addr . $row_addr;
								break;
								case 1:
								$range[] = $col_addr . $row_addr - $val;
								break;
								case 2:
								$range[] = $col_addr . $row_addr + $val;
								break;
								
							}
						}						
						$row[$colkey] = sprintf('=RANK(%s, %s)', $source, implode(':', $range));
					}
				}
			}
		}
					
		fputcsv($fp, $row);
	}
	fclose($fp);
	return true;
}

}
