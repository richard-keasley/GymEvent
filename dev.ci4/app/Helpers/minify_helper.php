<?php

function minify($string, $format='css') {
	// Remove comments
	$string = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $string);
	// Remove spaces before and after selectors, braces, and colons
	$string = preg_replace('/\s*([{}|:;,])\s+/', '$1', $string);
	// Remove remaining spaces and line breaks
	$search = ["\r\n", "\r", "\n", "\t", '    ', '   ', '  '];
	$string = str_replace($search, '', $string);
	return $string;
}

function minify_file($path) {
	if(is_file($path)) {
		$info = pathinfo($path);
		$format = $info['extension'] ?? null;
		$buffer = file_get_contents($path);
		return minify($buffer, $format);
	}

}