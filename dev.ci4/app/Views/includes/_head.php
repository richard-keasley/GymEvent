<!DOCTYPE html>
<html lang="en">
<head>
<?php
$viewdir = realpath(config('Paths')->viewDirectory);
include "{$viewdir}/includes/_head_common.php";
?>
</head>

<body class="container">

<header>
<?php 
if($showhelp ?? false) {
	// look for help entry
	$html = (new \App\Models\Htmls)->find_path();
	if($html) {
		$this->setVar('html', $html);
		echo $this->include('html/popup');
	}
}
?>
<h1><?php echo empty($heading) ? $title : $heading;?></h1>
<?php
include(__DIR__ . '/breadcrumbs.php');
include(__DIR__ . '/messages.php');
?>
</header>
