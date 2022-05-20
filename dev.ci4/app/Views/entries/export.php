<?php $this->extend('default');

$this->section('content');

$table = \App\Views\Htm\Table::load('bordered');

if($export) {
	switch($format) {
		case 'run':
		// sort by running order, number
		$sortby = []; $tbody = []; $rowsort = [];
		foreach($export as $row) {
			foreach($row['run'] as $key=>$val) {
				$rowsort["run.{$key}"] = str_pad($val, 3, ' ', STR_PAD_LEFT);
			}
			$rowsort['entry.num'] = str_pad($row['entry']['num'], 3, ' ', STR_PAD_LEFT);
			$sortby[] = implode('', $rowsort);
						
			$tbody[] = [
				'runorder' => implode(', ', $row['run']),
				'dis' => $row['dis']['abbr'],
				'cat' => $row['cat']['abbr'],
				'num' => $row['entry']['num'],
				'club' => $row['entry']['club']['abbr'],
				'name' => $row['entry']['name']
			];
		}
		# d($sortby);
		array_multisort($sortby, $tbody);

		$headings = ['runorder', 'dis', 'cat'];
		$cattable = new \App\Views\Htm\Cattable($headings);
		echo $cattable->htm($tbody);
		break;
		
		default:
		$tbody = [];
		foreach($export as $row) $tbody[] = array_flatten_with_dots($row);
		$table->setHeading(array_keys($tbody[0]));
		echo $table->generate($tbody);
	}
}

$this->endSection(); 

$this->section('top'); ?>

<div class="toolbar"><?php 
echo \App\Libraries\View::back_link("entries/view/{$event->id}");
echo getlink("/admin/entries/export/{$event->id}/csv", '<span class="bi-file-spreadsheet" title="Download scoreboard spreadsheet"></span>');
echo getlink("/admin/entries/export/{$event->id}/scoretable", '<span class="bi-table" title="Download score tables"></span>');
echo getlink("/admin/entries/export/{$event->id}/sql", '<span class="bi-file-code" title="Download SQL script"></span>');
if($format=='run') {
	echo getlink("/admin/entries/export/{$event->id}", '<span class="bi-list" title="View scoreboard data"></span>');
}
else {
	echo getlink("/admin/entries/export/{$event->id}/run", '<span class="bi-list-ol" title="View running order"></span>');
}
?></div>

<?php $this->endSection(); 

