<?php $this->extend('default');

$this->section('content');

if($export) {
	switch($format) {
		case 'run':
		$pad_length = []; $table_sort = []; $tbody = [];
		foreach($export as $row) {
			// sort by running order, discipline, category, number
			$rowsort = [
				$row['order'], // from entry->runorder
				$row['dis']['abbr'],
				$row['cat']['order'],
				$row['entry']['number']
			];
			$table_sort[] = $rowsort;
			$pad_length[] = max(array_map('strlen', $rowsort));
										
			$tbody[] = [
				'runorder' => implode(', ', $row['run']),
				'dis' => $row['dis']['name'],
				'cat' => $row['cat']['name'],
				'num' => $row['entry']['number'],
				'club' => $row['entry']['club']['shortName'],
				'name' => $row['entry']['title']
			];
		}

		$pad_length = max($pad_length);
		$pad_char = " ";
		$sortby = [];
		foreach($table_sort as $row) {
			$string = '';
			foreach($row as $val) {
				$string .= str_pad($val, $pad_length, $pad_char, STR_PAD_LEFT);	
			}
			$sortby[] = $string;
		}
		# d($table_sort, $pad_length, $sortby);
		array_multisort($sortby, $tbody);

		$headings = ['runorder', 'dis', 'cat'];
		$cattable = new \App\Views\Htm\Cattable($headings);
		echo $cattable->htm($tbody);
		break;
		
		default:
		$tbody = []; $thead = [];
		foreach($export as $rowkey=>$row) {
			$row = array_flatten_with_dots($row);
			if(!$thead) {
				foreach(array_keys($row) as $key) {
					$thead[] = str_replace('.', '_', $key);
				}
			}
			$tbody[] = $row;
		}
		$table = \App\Views\Htm\Table::load('bordered');
		$table->setHeading($thead);
		# d($thead);
		echo $table->generate($tbody);
	}
}

$this->endSection(); 

$this->section('top'); ?>

<div class="toolbar"><?php 
echo \App\Libraries\View::back_link("entries/view/{$event->id}");
echo getlink("/admin/entries/export/{$event->id}/csv", '<span class="bi-file-spreadsheet" title="Download scoreboard spreadsheet"></span>');
echo getlink("/admin/entries/export/{$event->id}/scoretable", '<span class="bi-table" title="Download score tables"></span>');
# echo getlink("/admin/entries/export/{$event->id}/sql", '<span class="bi-file-code" title="Download SQL script"></span>');
if($format=='run') {
	echo getlink("/admin/entries/export/{$event->id}", '<span class="bi-list" title="View scoreboard data"></span>');
}
else {
	echo getlink("/admin/entries/export/{$event->id}/run", '<span class="bi-list-ol" title="View running order"></span>');
}
?></div>

<?php $this->endSection(); 

