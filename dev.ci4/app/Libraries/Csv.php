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
		$row_idx = $rowkey + 1;
		foreach($row as $colkey=>$cell) {
			if(preg_match('#^\{.*\}$#i', $cell)) {
				$func = null;
				$params = [];
				$arr = explode(' ', trim($cell, '{}'));
				$arr = preg_split('#[\s*]#', trim($cell, '{}'));
				foreach($arr as $key=>$val) {
					if(!$key) $func = trim($val);
					elseif(strlen($val)) {
						$params[] = intval($val);
					}
				}
				
				switch($func) {
					case 'sum':
					if(count($params)!=2) break;
					$xy0 = [$params[0], $row_idx];
					$xy1 = [$params[1], $row_idx];
					$range = self::xl_range($xy0, $xy1);
					$row[$colkey] = "=SUM({$range})";
					break;
					
					case 'rank':
					if(count($params)!=3) break;
					$xy_src = [$params[0], $row_idx];
					$xy0 = [$params[0], $row_idx + $params[1]];
					$xy1 = [$params[0], $row_idx + $params[2]];
					$source = self::xl_address($xy_src);
					$range = self::xl_range($xy0, $xy1);
					$row[$colkey] = "=RANK({$source},{$range})";
					break;
				}
			}
		}
					
		fputcsv($fp, $row);
	}
	fclose($fp);
	return true;
}

static function xl_address($xy) {
	return chr($xy[0] + 64) . $xy[1];
}

static function xl_range($xy0, $xy1) {
	$range = [
		self::xl_address($xy0),
		self::xl_address($xy1)
	];
	return implode(':', $range);
	
	
	
}

}
