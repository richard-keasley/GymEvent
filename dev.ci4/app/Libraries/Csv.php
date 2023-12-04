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
			$xlcell = new xlcell($cellcol, $cellrow, $cell);
			$row[$colkey] = $xlcell->text;
		}
					
		fputcsv($fp, $row);
	}
	fclose($fp);
	return true;
}

}

class xlcell implements \stringable {

private $attributes = [];

function __construct($x, $y=0, $content='')  {
	$this->attributes = [
		'x' => intval($x),
		'y' => intval($y),
		'content' => strval($content)
	];
	$text = $this->attributes['content'];
	
	if(preg_match('#^=.+#i', $text)) {
		preg_match_all('#\[[^\]]+\]#', $text, $vals);
		$vals = $vals[0] ?? [];
		// covert [x,y] to A1 
		foreach(array_unique($vals) as $val) {
			$dxy = explode(',', trim($val, '[]'));
			$dx = $dxy[0] ?? 0;
			$dy = $dxy[1] ?? 0;
			$address = $this->address($dx, $dy);
			$text = str_replace($val, $address, $text);  
		}
	}
	$this->attributes['text'] = $text;
	# d($this->attributes);
}

function __get($key) {
	return $this->attributes[$key] ?? null;
}

function __toString() {
	return $this->text;
}

function address($dx='', $dy='') {
	$lock_char = '$';
	
	$int = intval(trim($dx, $lock_char));
	$x = $this->x + $int;
	$x = chr(64 + $x);
	$lock = strpos($dx, $lock_char)===0;
	if($lock) $x = "{$lock_char}{$x}";
	
	$int = intval(trim($dy, $lock_char));
	$y = $this->y + $int;
	$lock = strpos($dy, $lock_char)===0;
	if($lock) $y = "{$lock_char}{$y}";

	return "{$x}{$y}";
}

}
