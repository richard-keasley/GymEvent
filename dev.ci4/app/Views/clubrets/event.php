<?php $this->extend('default');
$table = new \CodeIgniter\View\Table();
$table->setTemplate(\App\Libraries\Table::templates['responsive']);

$this->section('content');?>
<div class="d-flex flex-wrap gap-3">
<?php foreach($event->participants() as $dis) { ?>
	<section class="mw-100">
	<?php
	foreach($dis['cats'] as $cat) { 	 
		$table->setHeading(['', 'club', 'name', 'DoB', '']);
		$tbody = [];
		foreach($cat['entries'] as $entkey=>$entry) {
			if(!$entry['club']) $entry['club'] = 'unknown <i class="bi bi-exclamation-triangle-fill text-warning"></i>';
			$tbody[] = [
				$entkey + 1,
				$entry['club'],
				$entry['name'],
				date('d-M-Y', $entry['dob']),
				$entry['opt']
			];
		}
		printf('<h5>%s - %s</h5>', $dis['name'], $cat['name']);
		echo $table->generate($tbody);
	}
	?>
	</section>
<?php } ?>
</div>
<?php 
# d($event->participants());
$this->endSection(); 

$this->section('top');
$attr = [
	'class' => "toolbar sticky-top"
];
echo form_open(base_url(uri_string()), $attr);
echo \App\Libraries\View::back_link($back_link);
echo $this->include('entries/populate/button');
echo form_close();
$this->endSection(); 

$this->section('sidebar');
?>
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
	$tbody[$rowkey]['fees'] = sprintf('&pound;&nbsp;%.2f', $fees[$rowkey]);
}
$tfoot = [sprintf('[%u clubs]', count($tbody))]; $thead = [''];
foreach($cols as $colkey) {
	$arr = array_column($tbody, $colkey);
	$tfoot[$colkey] = array_sum($arr);
	$thead[$colkey] = $colkey;
}
$tfoot[] = sprintf('&pound;&nbsp;%.2f', array_sum($fees));
$thead[] = '&pound;';

$table->setHeading($thead);
$table->setFooting($tfoot);
echo $table->generate($tbody);
?>

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
echo $table->generate($tbody);

$this->endSection(); 

$this->section('bottom'); 
echo $this->include('entries/populate/form');
$this->endSection(); 
