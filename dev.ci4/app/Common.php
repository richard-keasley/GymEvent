<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the frameworks
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @link: https://codeigniter4.github.io/CodeIgniter4/
 */

function getlink($path, $label='') {
	$path = trim($path, '/');
	if(!\App\Libraries\Auth::check_path($path)) return '';
	
	$attrs = ['class' => 'nav-link'];
		
	if(!$label) $label = basename($path);
		
	if($label=='edit') {
		$label = '<span class="bi bi-pencil"></span>';
		$attrs['class'] = 'btn btn-outline-primary';
		$attrs['title'] = "edit";
	}	
	if($label=='back') {
		$label = '<span class="bi bi-box-arrow-left"></span>';
		$attrs['class'] = 'btn btn-outline-secondary';
		$attrs['title'] = "close";
	}		
	if($label=='admin') {
		$label = '<span class="bi bi-gear"></span>';
		$attrs['class'] = 'btn btn-outline-secondary';
		$attrs['title'] = "admin";
	}		
	foreach(['/edit', '/add'] as $method) {
		if(strpos($path, $method)!==false) {
			$attrs['class'] = 'btn btn-outline-primary';
		}
	}
	return anchor($path, $label, $attrs);
}

function formatBytes($bytes, $precision=0) { 
    $units = ['B', 'KB', 'MB', 'GB', 'TB']; 

    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 

    // Uncomment one of the following alternatives
    # $bytes /= pow(1024, $pow);
    $bytes /= (1 << (10 * $pow)); 

    return round($bytes, $precision) . ' ' . $units[$pow]; 
} 

function csv_array($csv, $limit=null) {
	// take a line from form input and return array
	$csv = trim($csv);
	if($limit) {
		$arr = preg_split("/[\s,]+/", $csv, $limit);
		return array_pad($arr, $limit, '');
	}
	return $csv ? preg_split("/[\s,]+/", $csv) : [] ;
}

function filter_json($val, $array=1) {
	// convert POSTED json string to array
	$val = is_string($val) ? json_decode($val, $array) : null;
	if($val) return $val;
	return $array ? [] : new stdClass();
}

function filter_string($input) {
	// ensure POST value is valid string
	
	// return string
	$retval = (string) $input;
	
	// remove space variants (after copy and paste into input box)
	// https://unicode-explorer.com/b/2000
	$spaces = "~[\u{A0}\u{2000}\u{2001}\u{2002}\u{2003}\u{2004}\u{2005}\u{2006}\u{2007}\u{2008}\u{2009}\u{200A} ]+~";
	$retval = preg_replace($spaces, ' ', $retval);
	$retval = trim($retval, ", \n\r\t\v\x00");
	
	# d($input, $retval);
	return $retval;	
}