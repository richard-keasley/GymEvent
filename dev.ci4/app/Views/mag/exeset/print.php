<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
:root {
	font-size: 10.5pt;
}
body {
	font-family: sans-serif;
	margin: 1em;
	padding: 0;
}
pre {
	font-family: sans-serif;
	margin: 0;
	padding: 0;
}
h1 {
	margin: 0;
	background: #eee;
}
h3 {
	font-size: 1rem;
	margin: 0;
	padding: 0;
}
h5 {
	font-size: 1em;
	margin:0;
	padding: 0;
}
header {
	margin: 0 0 0.3em 0;
	border-bottom: 1px solid #b7561b;
	padding: 0.5em 0;
}
header ul {
	white-space: nowrap;
}
header ul strong {
	display: inline-block;
	min-width: 6em;
	text-align: right;
	margin-right: .3em;
}
main section {
	border-bottom: 1px solid #b7561b;
	padding: .2em 0;
	margin: 0;
	page-break-inside: avoid;
}

form {
	margin: 0.2em 0 0 0;
	font-size: 1rem;
	color: #212529;
	background-color: #ffc;
	padding: 0;
	border: 1px solid #F0E6BF;
	border-radius: .25rem;
	overflow: hidden;
}
form p {
	margin: 0.5em;
}
form button {
	float: left;
	font-weight: 400;
	line-height: 1.5;
	color: #FFF;
	text-align: center;
	cursor: pointer;
	background-color: #572b2b;
	border: 0;
	padding: .375rem .75rem;
	font-size: 1rem;
	border-radius: .25rem;
	margin: .5em;
}
table {
	border-collapse: collapse;
	border-collapse: collapse;
}
tfoot td {
	font-weight: bold;
	border-top: 1px solid #ddd;
}
thead th {
	text-align: left;
}

.alert-danger {
	background: #edd9d9a8;
	font-size: .9em;
}
.list-unstyled {
	list-style: none;
	padding: 0;
	margin: 0;
}
.row {
	display: flex;
	gap: 1em;
	align-items: flex-start;
}
.row > * {
	overflow:hidden;
}
.text-end {
	text-align: right;
}
.text-decoration-line-through {
	text-decoration: line-through;
}

.routine {
	border: 1px solid #666;
}
.routine table {
	width: 100%;
}
.routine td {
	border-top: 1px solid #eee;
	border-bottom: 1px solid #eee;
	padding: 0 .3em;
	white-space: nowrap;
}
.routine .el {
	width: 1em;
	color: #b7561b;
}
.routine .el0 {
	width: 1.5em;
}

@media print {
	@page {
		size: A4 portrait;
	}
	form {
		display:none;
	}
	body {
		margin: 0;
	}
}
</style>
<title><?php echo $title;?></title>
</head>

<body class="container-fluid">

<header class="row">

<div style="width:30%; font-weight:bold">
<?php echo $exeset->name;?><br>
<?php 
$options = \App\Libraries\Mag\Rules::index;
echo $options[$exeset->rulesetname] ?? '' ;
?>
</div>

<pre style="width:40%">
<?php echo $exeset->event;?>
</pre>

<div class="text-end" style="width:30%; font-size:0.8em; color:#777; font-style:italic;">
Saved: <?php 
	$time = new \CodeIgniter\I18n\Time($exeset->saved);
	echo $time->toLocalizedString('d MMM yyyy'); 
?><br>
Rules: <?php 
	$time = new \CodeIgniter\I18n\Time($exeset->ruleset->version);
	echo $time->toLocalizedString('d MMM yyyy'); 
?>
</div>

</header>

<main>
<?php  
foreach($exeset->exercises as $exekey=>$exercise) { 
	$exe_rules = $exeset->ruleset->exes[$exekey] ?? [] ;
	?>
	<section class="row">
	
	<h3 style="width:35%"><?php echo $exe_rules['name'];?></h3>
	
	<div class="routine" style="width:40%">
	<table>
	<tbody>
	<?php
		
	switch($exe_rules['method']) {
		case 'tariff':
			$dismount_num =  999;
			break;
		case 'routine':
		default: 
			$dismount_num = array_key_last($exercise['elements']); 
	}
	
	$tr = [];
	foreach($exercise['elements'] as $elnum=>$element) {
		foreach($element as $key=>$val) $tr[$key] = $val ? $val : '' ;
		?>
		<tr>
		<?php 
		printf('<td class="el">%s</td>', $elnum==$dismount_num ? 'D' : $elnum + 1);
		printf('<td class="el0">%s %s</td>', $tr[0], $tr[1]);
		printf('<td>%s</td>', $tr[2]);
		?>
		</tr>
		<?php 
	}
	?>
	</tbody>
	</table>
	</div>
	
	<div style="width:25%">
	<?php 
	$this->setData(['exekey' => $exekey]);
	echo $this->include('mag/exeset/exeval');	
	?>
	<ul style="margin-top:0.5em;" class="list-unstyled">
	<?php 
	foreach($exercise['neutrals'] as $nkey=>$nval) { 
		$decoration = $nval ? 'none' : 'line-through' ;
		$neutral = $exe_rules['neutrals'][$nkey]; 
		printf('<li class="text-decoration-%s">%s</li>', $decoration, $neutral['description']);
	} ?>
	</ul>
	</div>

	</section>
	<?php
} ?>
</main>

<footer>
<?php 
$action = site_url('mag/routine');
$attr = [];
echo form_open($action, $attr, $post);
?>
<button type="submit" title="re-edit these routines">edit</button>
<p>Print this page or 
<abbr title="right click, then select save as...">save it</abbr>
to your PC for use later. Use A4 portrait for printing; you may have to play with the margins to fit it on one page.</p>
<?php echo form_close();?>
</footer>

</body>
</html>
