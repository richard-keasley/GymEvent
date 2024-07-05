<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="robots" content="noindex,nofollow">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?php echo $title;?></title>
<?php 
echo \App\ThirdParty\jquery::script(); 
if(!empty($head)) echo $head;
?>
</head>

<body class="container-fluid">

<div id="printdata"></div>

<footer>

<p><a href="/ma2/routine" class="btn btn-primary" title="re-edit these routines">edit</a></p>
</footer>

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
