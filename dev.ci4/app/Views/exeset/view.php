<!DOCTYPE html>
<html lang="en">
<head>
<?php
$viewdir = realpath(config('Paths')->viewDirectory);
include "{$viewdir}/includes/_head_common.php";
?>
</head>

<body class="container-fluid">
<header class="d-print-none">
<?php 
$idxsel = [];
$attrs = [
	'id' => "exeset-editlink",
	'class' => "btn btn-primary bi bi-pencil-square",
	'title' => "Edit these routines",
	'href' => "", // set in JS after page load
];
$idxsel[] = sprintf('<a %s></a>', stringify_attributes($attrs));

$attrs = [
	'class' => "btn btn-primary bi bi-printer",
	'title' => "Print these routines",
	'onclick' => "window.print()",
];
$idxsel[] = sprintf('<a %s></a>', stringify_attributes($attrs));

$idxsel = implode(' ', $idxsel);
include __DIR__ . '/idxsel.php'; 
?>
</header>

<div id="viewdata"></div>

<script><?php
ob_start();
include __DIR__ . '/exesets.js';
?>
$(function() {
exesets.idx = exesets.idxsel.store();
var exeset = exesets.storage.load();
exesets.viewdata.set(exeset, '<?php echo $layout;?>');

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
