<?php

declare(strict_types=1);

namespace App\Format;

use CodeIgniter\Format\Exceptions\FormatException;
use Config\Format;

/**
 * CSV data formatter
 *
 */
class CSVFormatter implements \CodeIgniter\Format\FormatterInterface {
/**
 * Takes the given data and formats it.
 *
 * @param array<array-key, mixed>|object|string $data
 *
 * @return string
 */

public function format($data) {
	$config = new Format();
	
	/*
	https://www.php.net/manual/en/function.fputcsv.php
	*/
	$cfg = $config->formatterOptions['text/csv'] ?? [];
	$opt = [
		'sep' => ",",
		'enc' => "\"",
		'esc' => "\\",
		'eol' => "\r\n",
		'header' => true
	];
	foreach(array_keys($opt) as $key) {
		if(isset($cfg[$key])) $opt[$key] = $cfg[$key];
	}
	# print_r($opt);
		
	// ensure it's a valid input
	$quote_style = ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5;
	try {
		$tbody = [];
		$rowkey = '?'; $colkey = '?';
		
		if(!is_array($data)) throw new \exception('input is not an array');
		foreach($data as $rowkey=>$row) {
			if(!is_array($row)) throw new \exception('row is not an array');
			
			if($opt['header'] && (count($row)>1)) {
				$tbody[] = array_keys($row);
			}
			$opt['header'] = false;

			$tr = [];
			foreach($row as $colkey=>$cell) {
				if(is_array($cell)) throw new \exception('cell is an array');
				$tr[] = html_entity_decode((string) $cell, $quote_style, 'UTF-8');
			}
			$tbody[] = $tr;
		}	
	} 
	catch(\throwable $ex) {
		return $ex->getMessage() . " at [{$rowkey}, {$colkey}]";
	}
	# return print_r($tbody, true); 
	
	// convert to CSV
	$fp = fopen('php://output', 'w');
	if(!$fp) return 'could not open stream';
	ob_start();	
	foreach($tbody as $rowkey=>$row) {
		foreach($row as $colkey=>$cell) {
			$row[$colkey] = (new xlcell($colkey, $rowkey, $cell))->__toString();
			# $row[$colkey] = html_entity_decode((string) $xlcell, $quote_style, 'UTF-8');
		}
		fputcsv($fp, $row, $opt['sep'], $opt['enc'], $opt['esc'], $opt['eol']);
	}
	fclose($fp);
	return ob_get_clean();
}

}


class xlcell implements \stringable {

private $attributes = [];

function __construct($x=0, $y=0, $content='')  {
	// php arrays are zero base, excel spreadsheet is A1 based
	$this->attributes = [
		'x' => intval($x) + 1,
		'y' => intval($y) + 1,
		'content' => strval($content)
	];
	$text = $this->attributes['content'];
	
	// process if starts with equals
	if(preg_match('#^=.+#i', $text)) {
		// find all matches of [x,y] 
		preg_match_all('#\[[^\]]+\]#', $text, $vals);
		$vals = $vals[0] ?? [];
		// convert [x,y] to A1 
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
	$dx = strval($dx);
	$dy = strval($dy);
	
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
