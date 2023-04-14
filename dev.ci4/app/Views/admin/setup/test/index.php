<?php $this->extend('default');

$this->section('content');

$nav = [];
foreach(glob(__DIR__ . '/*.php') as $file) {
	$test_name = basename($file, '.php');
	$href = "setup/dev/test/{$test_name}";
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



$this->endSection();

$this->section('top'); ?>
<p class="alert alert-light">Random page to allow you to test ideas.</p>
<?php $this->endSection();