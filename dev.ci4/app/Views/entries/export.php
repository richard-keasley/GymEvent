<?php $this->extend('default');

$this->section('content');

$table = \App\Views\Htm\Table::load('bordered');

if($export) {
	if($format=='run') {
		// sort by running order, number
		$sortby = []; $tbody = []; $rowsort = [];
		foreach($export as $row) {
			$runorder = [];
			foreach($row as $key=>$val) {
				if(strpos($key, 'run_')===0) {
					$runorder[] = $val;
					$rowsort[$key] = str_pad($val, 3, ' ', STR_PAD_LEFT);
				}
			}
			$rowsort['entry_number'] = str_pad($row['entry_number'], 3, ' ', STR_PAD_LEFT);
			$sortby[] = implode('', $rowsort);
			$tbody[] = [
				'runorder' => implode(', ', $runorder),
				'dis' => $row['dis_abbr'],
				'cat' => $row['cat_abbr'],
				'num' => $row['entry_number'],
				'club' => $row['entry_club_shortName'],
				'name' => $row['entry_title']
			];
		}
		array_multisort($sortby, $tbody);

		$headings = ['runorder', 'dis', 'cat'];
		$cattable = new \App\Views\Htm\Cattable($headings);
		echo $cattable->htm($tbody);
		
	}
	else {
		$table->setHeading(array_keys($export[0]));
		echo $table->generate($export);
	}
}

$this->endSection(); 

$this->section('top'); ?>

<div class="toolbar"><?php 
echo \App\Libraries\View::back_link("entries/view/{$event->id}");
echo getlink("/admin/entries/export/{$event->id}/csv", '<span class="bi-file-spreadsheet" title="Export as spreadsheet"></span>');
echo getlink("/admin/entries/export/{$event->id}/sql", '<span class="bi-file-code" title="Get SQL script"></span>');
if($format=='run') {
	echo getlink("/admin/entries/export/{$event->id}", '<span class="bi-list" title="Show export"></span>');
}
else {
	echo getlink("/admin/entries/export/{$event->id}/run", '<span class="bi-list-ol" title="Show running order"></span>');
}
?></div>

<?php $this->endSection(); 

