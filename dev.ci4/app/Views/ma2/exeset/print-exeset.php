<header class="row">

<div class="col-6 col-sm-4"><strong><?php 
$options = \App\Libraries\Mag\Rules::index;
$lines = [
	$exeset->name,
	$options[$exeset->rulesetname] ?? ''
];
echo implode('<br>', $lines);
?></strong></div>

<pre class="col-6 col-sm-4"><?php
 echo $exeset->event;
?></pre> 

<div class="text-end fst-italic text-muted d-none d-sm-block col-sm-4"><?php
$lines = []; $format = 'd MMM yyyy';
$time = new \CodeIgniter\I18n\Time($exeset->saved);
$lines[] = 'Saved: ' . $time->toLocalizedString($format); 
$time = new \CodeIgniter\I18n\Time($exeset->ruleset->version);
$lines[] = 'Rules: ' . $time->toLocalizedString($format); 
echo implode('<br>', $lines);
?></div>

</header>

<main><?php  
foreach($exeset->exercises as $exekey=>$exercise) { 
	$exe_rules = $exeset->ruleset->exes[$exekey] ?? [] ;
	?>
	<section class="row">
	
	<h5 class="col-6 col-sm-4"><?php echo $exe_rules['name'];?></h5>
	
	<div class="routine d-none d-sm-block col-sm-5">
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
	
	<div class="col-6 col-sm-3">
	<div class="d-flex-column">
	<?php 
	$this->setData(['exekey' => $exekey]);
	echo $this->include('ma2/exeset/exeval');	
	?>
	<ul class="list-unstyled">
	<?php 
	foreach($exercise['neutrals'] as $nkey=>$nval) { 
		$decoration = $nval ? 'none' : 'line-through' ;
		$neutral = $exe_rules['neutrals'][$nkey]; 
		printf('<li class="text-decoration-%s">%s</li>', $decoration, $neutral['description']);
	} ?>
	</ul>
	</div>
	</div>

	</section>
	<?php
}

?></main>

<?php
# d($exeset);
