<!DOCTYPE html>
<html lang="en">
<head>
<?php 
$viewdir = realpath(config('Paths')->viewDirectory);
include "{$viewdir}/includes/_head_common.php";

$minifier = new MatthiasMullie\Minify\CSS("{$viewdir}/kiosk.css");
$buffer = $minifier->minify();
if($buffer) echo "<style>{$buffer}</style>";
?>
</head>
<body id="kiosk">
<div id="container">
<div id="flex"><?php 
echo $this->renderSection('content');
?></div>

<footer><?php 
echo base_url($link);
?></footer>

<img id="logo" src="/app/profile/logo.png">

</div>
</body>
</html>
