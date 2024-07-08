<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="robots" content="noindex,nofollow">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?php echo $title;?></title>
<?php 
echo \App\ThirdParty\jquery::script(); 
echo link_tag('app/gymevent.css');
if(!empty($head)) echo $head;
?>
</head>

<body class="container-fluid">
<header>
<?php 
$attrs = [
	'class' => "btn btn-primary bi bi-pencil-square",
	'title' => "Edit this exercise set",
	'href' => "/ma2/routine",
];
$idxsel = sprintf('<a %s></a>', stringify_attributes($attrs));
include __DIR__ . '/idxsel.php'; 
?>
</header>

<main id="printdata"></main>

<script><?php
ob_start();
include __DIR__ . '/exesets.js';
echo ob_get_clean();
/*

$minifier = new MatthiasMullie\Minify\JS();
$minifier->add(ob_get_clean());
echo $minifier->minify();
*/
?>

$(function() {
exesets.idx = localStorage.getItem('mag-exesets-idx') ?? 0;
var exeset = exesets.storage.load();
exesets.printdata.set(exeset);
});
</script>

</body>
</html>
