<?php

declare(strict_types=1);

namespace App\Format;

use CodeIgniter\Format\Exceptions\FormatException;
use Config\Format;
use SimpleXMLElement;

/**
 * XML data formatter
 *
 */
class XMLFormatter extends \CodeIgniter\Format\XMLFormatter {


protected function normalizeXMLTag($key) {
	$startChar = 'A-Z_a-z' .
		'\\x{C0}-\\x{D6}\\x{D8}-\\x{F6}\\x{F8}-\\x{2FF}\\x{370}-\\x{37D}' .
		'\\x{37F}-\\x{1FFF}\\x{200C}-\\x{200D}\\x{2070}-\\x{218F}' .
		'\\x{2C00}-\\x{2FEF}\\x{3001}-\\x{D7FF}\\x{F900}-\\x{FDCF}' .
		'\\x{FDF0}-\\x{FFFD}\\x{10000}-\\x{EFFFF}';
	$validName = $startChar . '\\.\\d\\x{B7}\\x{300}-\\x{36F}\\x{203F}-\\x{2040}';

	$key = (string) $key;

	$key = trim($key);
	$key = preg_replace("/[^{$validName}-]+/u", '', $key);
	
	// allows spreadsheets to work better (each row has same tag name)
	# $key = preg_replace("/^[^{$startChar}]+/u", 'item$0', $key);
	$key = preg_replace("/^[^{$startChar}]+/u", 'item', $key);

	return preg_replace('/^(xml).*/iu', 'item$0', $key); // XML is a reserved starting word
}

}
