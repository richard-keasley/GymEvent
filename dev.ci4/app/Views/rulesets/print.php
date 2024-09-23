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
<header class="d-print-none">
<?php 
$attrs = [
	'id' => "exeset-editlink",
	'class' => "btn btn-primary bi bi-pencil-square",
	'title' => "Edit these routines",
	'href' => "", // set in JS after page load
];
$idxsel = sprintf('<a %s></a>', stringify_attributes($attrs));
include __DIR__ . '/idxsel.php'; 
?>
</header>

<div id="printdata"></div>

<script><?php
ob_start();
include __DIR__ . '/exesets.js';
?>
$(function() {
exesets.idx = exesets.idxsel.store();
var exeset = exesets.storage.load();
exesets.printdata.set(exeset);

$('#exeset-editlink')[0].href = exesets.site_url('routine');

});
<?php 
if(ENVIRONMENT=='development') { 
	echo ob_get_clean();
}
else { 
	$minifier = new MatthiasMullie\Minify\JS();
	$minifier->add(ob_get_clean());
	echo $minifier->minify();
}
?>
</script>

</body>
</html>
