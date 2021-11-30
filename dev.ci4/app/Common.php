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
 
define('VIEWPATH', APPPATH . 'Views/');
define('PUBLICPATH', $_SERVER['DOCUMENT_ROOT']);

function getlink($path, $label='') {
	$path = trim($path, '/');
	if(!\App\Libraries\Auth::check_path($path)) return '';
		
	$attr = [
		'class' => 'nav-link'
	];
		
	if(!$label) $label = basename($path);
		
	if($label=='edit') {
		$label = '<span class="bi bi-pencil"></span>';
		$attr['class'] = 'btn btn-outline-primary';
		$attr['title'] = "edit";
	}	
	if($label=='back') {
		$label = '<span class="bi bi-box-arrow-left"></span>';
		$attr['class'] = 'btn btn-outline-secondary';
		$attr['title'] = "close";
	}		
	foreach(['/edit', '/add'] as $method) {
		if(strpos($path, $method)!==false) {
			$attr['class'] = 'btn btn-outline-primary';
		}
	}
	return anchor(base_url($path), $label, $attr);
}

function formatBytes($bytes, $precision = 2) { 
    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 

    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 

    // Uncomment one of the following alternatives
    //$bytes /= pow(1024, $pow);
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