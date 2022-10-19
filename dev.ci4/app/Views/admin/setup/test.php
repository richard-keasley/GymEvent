<?php $this->extend('default');

$this->section('content');
$clubret = new \App\Entities\Clubret;


$testdata = [
	'name1, name2, 12346, 7-8-2010',
	' name1, name2, 12346, 12 aug 2010    ',
	'123456, name1, name2, 12 aug 2010    ',
	'123456, name1, name2, 12 aug 2021',
	'123456, 12 aug 2010, name1, name2    ',
	'name1, name2, 12346, dunno',
	'name1, name2, 12346',
	'name1, name2, 0, dunno',
	'',
	'name1',
	'3 aug 1990',
	'465, 5/3/90'
];

foreach($testdata as $key=>$row) {
	$namestring = new \App\Entities\namestring($row);
	$error = $namestring->error();
	echo "<p><strong>{$row}</strong><br>{$namestring}";
	if($error) printf('<br><span class="text-danger">Row %s %s</span>', $key, $error);
	echo "</p>";
}

$this->endSection();

$this->section('top'); ?>
<p class="alert alert-light">Random page to allow you to test ideas.</p>
<?php $this->endSection();