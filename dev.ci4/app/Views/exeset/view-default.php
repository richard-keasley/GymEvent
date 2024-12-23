<header class="row">

<div class="col-6 col-sm-4"><strong><?php 
$lines = [
	$exeset->name,
	\App\Libraries\Rulesets::title($exeset->rulesetname)
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
	$exe_rules = $exeset->ruleset->$exekey;
	?>
	<section class="row">
	
	<h5 class="col-6 col-sm-1"><?php echo $exekey;?></h5>
	
	<div class="routine d-none d-sm-block col-sm-6">
	<table>
	<tbody>
	<?php
		
	$tr = [];
	$has_dismount = $exe_rules['dismount'] ?? false;
	# d($exe_rules, $has_dismount);
	$last_elkey = array_key_last($exercise['elements']); 
	foreach($exercise['elements'] as $elkey=>$element) {
		$empty = true;
		foreach($element as $key=>$val) {
			if($val) $empty = false; else $val = '';
			$tr[$key] = $val;
		}
		if($empty) continue;
		
		$elnum = $has_dismount && $elkey==$last_elkey ? 
			'D' : 
			$elkey + 1;
		
		$row = [
			'elnum' => $elnum,
			'elval' => "{$tr[0]} {$tr[1]}",
			'eldes' => new \App\Views\Htm\Pretty($tr[2])
		];
		
		echo '<tr>';
		foreach($row as $class=>$td) {
			printf('<td class="%s">%s</td>', $class, $td);
		}
		echo '</tr>';
	}
	?>
	</tbody>
	</table>
	</div>
	
	<div class="dscore col-6 col-sm-5">
	<div class="d-flex-column">
	<?php 
	$this->setData(['exekey' => $exekey]);
	echo $this->include('exeset/exeval');
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
