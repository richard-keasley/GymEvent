<?php $this->extend('default');

$this->section('top'); ?>
<ul class="list-group">
<?php
$format = '<li class="list-group-item list-group-item-%s">%s.</li>';

$state = intval($event->clubrets);
$colour = match($state) {
	1 => 'success',
	2 => 'warning',
	default => 'danger'
};
$message = 'Entries for this event are ';
$message .= match($state) {
	1 => 'open',
	2 => 'completed',
	default => 'closed'
};
printf($format, $colour, $message);

$state = intval($event->music);
$colour = match($state) {
	1 => 'success',
	2 => 'warning',
	default => 'danger'
};
$message = 'Music upload is ';
$message .= match($state) {
	1 => 'open',
	2 => 'completed',
	default => 'inactive'
};
printf($format, $colour, $message);
?>
</ul>
<?php $this->endSection();

$this->section('content'); 
# d($event);

// start returns summary
if($event->clubrets==1) {
echo '<h3>Returns summary</h3>';

$fees = []; $cols = []; $count = []; $tbody = [];
foreach($event->clubrets() as $rowkey=>$clubret) {
	$user = $clubret->user;
	$label = $user ? $user->name : '[unknown]' ;
	
	$tbody[$rowkey] = [
		'club' => $label,
		'updated' => $clubret->updated
	];
			
	$count[$rowkey] = [];
	foreach($clubret->participants as $participant) {
		$dis = $participant['dis'];
		if(empty($count[$rowkey][$dis])) $count[$rowkey][$dis] = 0;
		$count[$rowkey][$dis]++;
		if(!in_array($dis, $cols)) $cols[] = $dis;
	}
		
	$cr_fees = $clubret->fees('fees');
	$fees[$rowkey] = array_sum(array_column($cr_fees, 1));
}

foreach($tbody as $rowkey=>$row) {
	foreach($cols as $colkey) {
		$val = $count[$rowkey][$colkey] ?? 0;
		$tbody[$rowkey][$colkey] = $val;
	}
	$tbody[$rowkey]['fees'] = $fees[$rowkey];
}

// add table cell formatting
$thead = []; $tfoot = [];
foreach($tbody as $rowkey=>$row) {
	if(!$thead) {
		foreach($row as $key=>$val) {
			$thead[$key] = ($key);
			$arr = array_column($tbody, $key);
			$tfoot[$key] = match($key) {
				'club' => count($arr),
				'updated' => '',
				'fees' => \App\Views\Htm\Table::money(array_sum($arr)),
				default => \App\Views\Htm\Table::number(array_sum($arr))
			};
		}
	}
	foreach($row as $key=>$val) {
		$tbody[$rowkey][$key] = match($key) {
			'club' => $row[$key],
			'updated' => \App\Views\Htm\Table::date($val),
			'fees' => \App\Views\Htm\Table::money($val),
			default => \App\Views\Htm\Table::number($val)
		};
	}
}
	
$table = \App\Views\Htm\Table::load('responsive');
$table->setHeading($thead);
$table->setFooting($tfoot);	
# d($tbody);
echo $table->generate($tbody);
	
?>
<h3>Participants</h3>
<div class="row">
<?php
$table = \App\Views\Htm\Table::load('small');
foreach($event->participants() as $dis) { ?>
	<div class="col-auto">
	<?php 
	$tbody = []; $total = 0;
	foreach($dis['cats'] as $cat) { 
		$count = count($cat['entries']);
		$total += $count;
		$tbody[] = [$cat['name'], ['data'=>$count,'class'=>"text-end"]];
	}
	$table->setHeading([$dis['name'], ['data'=>$total,'class'=>"text-end"]]);
	echo $table->generate($tbody);
	?>
	</div>
<?php } ?>
</div>

<?php }
// end returns summary

$this->endSection();





$this->section('bottom'); ?>
<div class="toolbar nav"><?php
echo \App\Libraries\View::back_link($back_link);
echo $event->link('player');
echo $event->link('teamtime');
?></div>
<?php  $this->endSection(); 
