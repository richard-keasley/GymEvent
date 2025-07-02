<?php

namespace App\Libraries\Toolbar;

use CodeIgniter\Debug\Toolbar\Collectors\BaseCollector;

class Permissions extends BaseCollector {

protected $hasTabContent = true;
protected $hasLabel = true;
protected $title = 'Permissions';

function display() {
ob_start();
echo '<ul style="list-style:none">';
$format = '<li style="line-height:normal;color:%s" title="%s"><strong>%s :</strong> %s</li>';
foreach(\App\Libraries\Auth::check_paths() as $path=>$row) {
	// $row = [ perm_name, perm_granted ]
	$colour = $row[1] ? '#0c0' : '#c00' ;
	$title = $row[1] ? 'allowed' : 'forbidden' ;
	printf($format, $colour, $title, $path, $row[0]);
};
echo '</ul>';
return ob_get_clean();
}

public function icon() : string {
	return 	'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNiIgaGVpZ2h0PSIxNiIgZmlsbD0iY3VycmVudENvbG9yIiBjbGFzcz0iYmkgYmktbG9jayIgdmlld0JveD0iMCAwIDE2IDE2Ij4KICA8cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik04IDBhNCA0IDAgMCAxIDQgNHYyLjA1YTIuNSAyLjUgMCAwIDEgMiAyLjQ1djVhMi41IDIuNSAwIDAgMS0yLjUgMi41aC03QTIuNSAyLjUgMCAwIDEgMiAxMy41di01YTIuNSAyLjUgMCAwIDEgMi0yLjQ1VjRhNCA0IDAgMCAxIDQtNE00LjUgN0ExLjUgMS41IDAgMCAwIDMgOC41djVBMS41IDEuNSAwIDAgMCA0LjUgMTVoN2ExLjUgMS41IDAgMCAwIDEuNS0xLjV2LTVBMS41IDEuNSAwIDAgMCAxMS41IDd6TTggMWEzIDMgMCAwIDAtMyAzdjJoNlY0YTMgMyAwIDAgMC0zLTMiLz4KPC9zdmc+';
}

}