<?php $this->extend('default');
$table = new \CodeIgniter\View\Table();
$template = ['table_open' => '<table class="table">'];
$table->setTemplate($template);
#$clubret = new \App\Entities\Clubret;

$this->section('content');
foreach($event->participants() as $dis) { 
	foreach($dis['cats'] as $cat) { 
		$table->setHeading(['#', 'club', 'name', 'DoB']);
		$tbody = [];
		foreach($cat['entries'] as $entkey=>$entry) {
			if(!$entry['club']) $entry['club'] = 'unknown <i class="bi bi-exclamation-triangle-fill text-warning"></i>';
			$tbody[] = [
				$entkey + 1,
				$entry['club'],
				$entry['name'],
				date('d-M-Y', $entry['dob'])
			];
		}
		printf('<h6>%s - %s</h6>', $dis['name'], $cat['name']);
		printf('<div class="table-responsive">%s</div>', $table->generate($tbody));
	}
}
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
$fees = []; $cols = []; $rows = [];
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
	
	$fees[$rowkey] = $clubret->fees('fees');
	foreach(array_keys($fees[$rowkey]) as $dis_name) {
		if(!in_array($dis_name, $cols)) $cols[] = $dis_name;
	}
}

$tbody = [];
foreach($rows as $rowkey=>$club) {
	$tbody[$rowkey] = [$club];
	$rowtot = 0 ;
	foreach($cols as $colkey) {
		$tbody[$rowkey][$colkey] = empty($fees[$rowkey][$colkey]) ? 0 : $fees[$rowkey][$colkey][1];
		$rowtot += $tbody[$rowkey][$colkey];
	}
	$tbody[$rowkey][] = $rowtot;
}
$tfoot = [0=>0]; $thead = [''];
foreach($cols as $colkey) {
	$arr = array_column($tbody, $colkey);
	$tfoot[$colkey] = array_sum($arr);
	$thead[$colkey] = $colkey;
}
$tfoot[] = array_sum($tfoot);
$thead[] = 'TOT';
$tfoot[0] = 'Total';

$table->setHeading($thead);
$table->setFooting($tfoot);
printf('<div class="table-responsive">%s</div>', $table->generate($tbody));

echo '<h4>Staff</h4>';
$tbody = [];
foreach($event->staff() as $entkey=>$entry) {
	$tbody[] = [
		# $entkey + 1,
		$entry['club'],
		$entry['cat'],
		$entry['name'],
		$entry['bg'],
		# date('d-M-Y', $entry['dob'])
	];
}
$table->setHeading(['club', 'type', 'name', 'BG']);
$table->setFooting([]);
printf('<div class="table-responsive">%s</div>', $table->generate($tbody));

$this->endSection(); 

$this->section('bottom'); 
echo $this->include('entries/populate/form');
$this->endSection(); 
