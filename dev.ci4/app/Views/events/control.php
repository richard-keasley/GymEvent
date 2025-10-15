<?php $this->extend('default');

$this->section('top'); ?>
<ul class="list-group mb-3">
<?php
$format = '<li class="list-group-item list-group-item-%s">%s</li>';

$state = intval($event->clubrets);
$colour = match($state) {
	1 => 'success',
	2 => 'warning',
	default => 'danger'
};
$message = match($state) {
	1 => 'Entries for this event are open.',
	2 => 'Entries for this event are completed. Entries may have been edited since the closing date.',
	default => 'Entries for this event are closed.'
};
printf($format, $colour, $message);

$state = intval($event->music);
$colour = match($state) {
	1 => 'success',
	2 => 'warning',
	default => 'danger'
};
$message = match($state) {
	1 => 'Music upload is open.',
	2 => 'Music upload is completed.',
	default => 'Music upload is inactive.'
};
printf($format, $colour, $message);
?>
</ul>
<?php $this->endSection();

$this->section('content'); 
# d($event);

$items = [];
$format = '<p class="p-1"><a href="?dl=%1$s" class="btn btn-secondary" title="Download %1$s as spreadsheet">download <span class="bi-download"></span></a></p>%2$s';

foreach($tables as $tbl_key=>$tbody) {
	// add table cell formatting
	$layout = 'table';
	$thead = []; $tfoot = []; $headings = false;
	switch($tbl_key) {
		case 'club_returns':
		foreach($tbody as $rowkey=>$row) {
			if(!$thead) {
				foreach($row as $key=>$val) {
					$thead[$key] = $key;
					$arr = array_column($tbody, $key);
					$tfoot[$key] = match($key) {
						'club' => count($arr),
						'email' => '',
						'staff' => '',
						'terms' => '',
						'updated' => '',
						'fees' => \App\Views\Htm\Table::money(array_sum($arr)),
						default => \App\Views\Htm\Table::number(array_sum($arr))
					};
				}
			}
			foreach($row as $key=>$val) {
				$tbody[$rowkey][$key] = match($key) {
					'club' => $val,
					'email' => $val,
					'staff' => $val ? 
						'<span class="bi-check text-success"></span>' : 		
						'<span class="bi-x text-danger"></span>' ,
					'terms' => $val ? 
						'<span class="bi-check text-success"></span>' : 		
						'<span class="bi-x text-danger"></span>' ,
					'updated' => \App\Views\Htm\Table::date($val),
					'fees' => \App\Views\Htm\Table::money($val),
					default => \App\Views\Htm\Table::number($val)
				};
			}
		}
		break;
		
		case 'categories':
		foreach($tbody as $rowkey=>$row) {
			if(!$thead) {
				$thead = array_keys($row);
				$arr = array_column($tbody, 'count');
				$tfoot = [
					'', 
					\App\Views\Htm\Table::number(count($arr)), 
					\App\Views\Htm\Table::number(array_sum($arr))
				];
			}
			$tbody[$rowkey]['count'] = \App\Views\Htm\Table::number($row['count']);
		}
		break;
		
		case 'staff':
		foreach($tbody as $rowkey=>$row) {
			if(!$thead) {
				$thead = array_keys($row);
			}
		}
		break;
		
		case 'participants':
		foreach($tbody as $rowkey=>$row) {
			if(!$thead) {
				$thead = array_keys($row);
			}
			$tbody[$rowkey]['DoB'] = \App\Views\Htm\Table::date($row['DoB']);
		}
		break;
		
		case 'running_order':
		$layout = 'cattable';
		$thead = false;
		$headings = ['runorder', 'dis', 'cat'];
		break;
	}
	
	$data = [
		'thead' => $thead,
		'tfoot' => $tfoot,
		'headings' => $headings,
		'export' => $tbody,
	];
	
	$items[] = [
		'heading' => humanize($tbl_key),
		'content' => sprintf($format, $tbl_key, view("export/{$layout}", $data)),
	];
}

echo new \App\Views\Htm\Tabs($items);

$this->endSection();

$this->section('bottom'); ?>
<div class="toolbar nav"><?php
echo \App\Libraries\View::back_link($back_link);
echo $event->link('player');
echo $event->link('teamtime');
?></div>
<?php  $this->endSection(); 
