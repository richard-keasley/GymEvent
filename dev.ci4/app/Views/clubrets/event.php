<?php $this->extend('default');
$table = new \CodeIgniter\View\Table();
$template = ['table_open' => '<table class="table">'];
$table->setTemplate($template);
#$clubret = new \App\Entities\Clubret;

$this->section('content');
foreach($event->participants() as $dis) { 
	foreach($dis['cats'] as $cat) { 
		$table->setHeading(['#', 'name', 'club', 'DoB']);
		$tbody = [];
		foreach($cat['entries'] as $entkey=>$entry) {
			array_unshift($entry, $entkey + 1);
			unset($entry['user_id']);
			$entry['dob'] = date('d-M-Y', $entry['dob']);
			$tbody[] = $entry;
		}
		printf('<h6>%s - %s</h6>', $dis['name'], $cat['name']);
		echo $table->generate($tbody);
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
	$label = $user->name;
	$ok = $clubret->check();
	if(!$ok) $label .= ' <span class="bi bi-x-circle text-danger" title="There are errors in this return"></span>';
	if($user->deleted_at) $label .= ' <span class="bi bi-trash text-danger" title="This user is disabled"></span>';
	$rows[$rowkey] = getlink($clubret->url('view', 'admin'), $label);
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
echo $table->generate($tbody);
$this->endSection(); 

$this->section('bottom'); 
echo $this->include('entries/populate/form');
$this->endSection(); 
