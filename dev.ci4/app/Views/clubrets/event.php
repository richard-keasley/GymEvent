<?php $this->extend('default');
$table = \App\Views\Htm\Table::load('responsive');

$this->section('content');?>
<div class="d-flex flex-wrap gap-3 d-print-block mb-1">

<section class="mw-100">
<h4>Payments due</h4>
<?php
$fees = []; $cols = []; $rows = []; $count = [];
foreach($clubrets as $rowkey=>$clubret) {
	$user = $clubret->user();
	if($user) {
		$label = $user->name;
		if($user->deleted_at) $label .= ' <i class="bi bi-x-circle text-danger" title="This user is disabled"></i>';		
	}
	else $label = 'unknown <i class="bi bi-exclamation-triangle-fill text-warning"></i>';
	
	$ok = $clubret->check();
	if(!$ok) $label .= ' <span class="bi bi-exclamation-triangle-fill text-warning" title="There are errors in this return"></span>';
	
	$rows[$rowkey] = getlink($clubret->url('view', 'admin'), $label);
	if($user) $rows[$rowkey] .= ' ' . $user->link();
	
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

$tbody = [];
foreach($rows as $rowkey=>$club) {
	$tbody[$rowkey] = [$club];
	foreach($cols as $colkey) {
		$tbody[$rowkey][$colkey] = $count[$rowkey][$colkey] ?? 0;

	}
	$tbody[$rowkey]['fees'] = \App\Views\Htm\Table::money($fees[$rowkey]);
}
$thead = [''];
$tfoot = [sprintf('[%u clubs]', count($tbody))]; 
foreach($cols as $colkey) {
	$arr = array_column($tbody, $colkey);
	$tfoot[$colkey] = array_sum($arr);
	$thead[$colkey] = $colkey;
}
$thead[] = '&pound;';
$tfoot[] = \App\Views\Htm\Table::money(array_sum($fees));

$table->setHeading($thead);
$table->setFooting($tfoot);
echo $table->generate($tbody);
?>
</section>

<section class="mw-100">
<h4>Staff</h4>
<?php
$tbody = [];
foreach($event->staff() as $entkey=>$entry) {
	$tbody[] = [
		# $entkey + 1,
		$entry['club'],
		humanize($entry['cat']),
		$entry['name'],
		$entry['bg'],
		# date('d-M-Y', $entry['dob'])
	];
}
$table->setHeading(['club', 'type', 'name', 'BG']);
$table->setFooting([]);
echo $table->generate($tbody);#?>
</section>

</div>

<div class="d-flex flex-wrap gap-3 d-print-block"><?php
foreach($event->participants() as $dis) { 
	foreach($dis['cats'] as $cat) { 	 
		# $table->setHeading(['', 'club', 'name', 'DoB', '']);
		$table->autoHeading = false;
		$tbody = [];
		foreach($cat['entries'] as $entkey=>$entry) {
			if(!$entry['club']) $entry['club'] = 'unknown <i class="bi bi-exclamation-triangle-fill text-warning"></i>';
			$tbody[] = [
				$entkey + 1,
				$entry['club'],
				$entry['name'],
				date('d-M-Y', $entry['dob']),
				humanize($entry['opt'])
			];
		}
		echo '<section class="mw-100">';

		printf('<h4>%s - %s</h4>', $dis['name'], $cat['name']);
		echo $table->generate($tbody);
		echo '</section>';
	}
} 
?></div>

<?php 
$this->endSection(); 

$this->section('top');
$attr = [
	'class' => "toolbar sticky-top"
];
echo form_open(base_url(uri_string()), $attr);
echo \App\Libraries\View::back_link($back_link); 
if(isset($users_dialogue)) { ?>
	<button type="button" class="btn btn-success bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#modalUser" title="Add new club return to this event"></button>
	<?php
}
echo $this->include('entries/populate/button');
echo form_close();
if(isset($users_dialogue)) { 
	echo $this->include('includes/users/dialogue');
}
$this->endSection(); 

/*
$this->section('sidebar');
$this->endSection(); 
*/

$this->section('bottom'); 
echo $this->include('entries/populate/form');
# d($event->participants());
d($event);

$this->endSection(); 
