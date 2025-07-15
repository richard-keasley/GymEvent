<?php $this->extend('default');

$this->section('content');

$nav = [];
foreach(glob(__DIR__ . '/*.php') as $file) {
	$test_name = basename($file, '.php');
	$href = "setup/dev/test/{$test_name}";
	$test_name = str_replace('_', ' ', $test_name);
	$nav[] = [$href, $test_name];
};
echo new \App\Views\Htm\Navbar($nav);

/*
use \App\Libraries\Teamtime as tt_lib;

foreach(['nothing', 'displays'] as $varname) {
	foreach([null, 1, 99] as $key) {
		echo "<h3>{$varname} / {$key}</h3>";
		d(tt_lib::get_value($varname, $key));

	}
}

*/

/*

echo "<pre>filter_json";
$arr = [
	null,
	1245,
	'string',
	[1,2,3],
	new stdClass(),
	'[1,2,3]',
	'{"fred":"name", "lsat":"name2"}',
	'[1, [20, 21, 22], 3]',
	'["error", [20, 21, 22] 3]'
];
foreach($arr as $json) {
	echo "\n===\nsend:\n"; var_dump($json);
	echo "array\n"; var_dump(filter_json($json));
	echo "object\n"; var_dump(filter_json($json, 0));
}
echo '</pre>';
// */


$this->endSection();

$this->section('top'); ?>
<p class="alert alert-light">Random page to allow you to test ideas.</p>
<?php $this->endSection();