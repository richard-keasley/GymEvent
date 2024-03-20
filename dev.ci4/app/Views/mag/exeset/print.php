<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style><?php
helper('minify');
echo minify_file(__DIR__ . '/print.css');
?></style>

<title><?php echo $title;?></title>
</head>

<body class="container-fluid">

<header class="row">

<div class="col-4" style="font-weight:bold">
<?php echo $exeset->name;?><br>
<?php 
$options = \App\Libraries\Mag\Rules::index;
echo $options[$exeset->rulesetname] ?? '' ;
?>
</div>

<pre class="col-4">
<?php echo $exeset->event;?>
</pre>

<div class="text-end col-4 text-muted d-none d-sm-block">
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
	
	<h3 class="col-4"><?php echo $exe_rules['name'];?></h3>
	
	<div class="col-5 routine">
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
	
	<div class="col-3">
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
<p>Print this page (use A4 portrait) or 
<abbr title="right click, then select save as...">save it</abbr>
to your PC for use later.</p>
<?php echo form_close();?>
</footer>

</body>
</html>
