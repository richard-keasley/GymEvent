<?php $this->extend('default');

$this->section('content');

$table = \App\Views\Htm\Table::load('bordered');

if($export) {
	if($format=='run') {
		$tables = []; $sortby = [];
		$newrow = [];
		foreach($export as $entry) {
			$runorder = [];
			foreach($entry as $key=>$val) {
				if(strpos($key, 'run_')===0) {
					$runorder[] = $val;
				}
			}
			$runsort = implode('_', $runorder);
			if(!isset($tables[$runsort])) {
				$tables[$runsort]['title'] = implode(', ', $runorder);
				$tables[$runsort]['tbody'] = [];
			}
			
			$tables[$runsort]['tbody'][] = [
				'dis' => $entry['dis_abbr'],
				'cat' => $entry['cat_abbr'],
				'club' => $entry['entry_club_shortName'],
				'num' => $entry['entry_number'],
				'name' => $entry['entry_title']
				
			];
		}
		ksort($tables);
		foreach($tables as $runtable) {
			echo "<h3>{$runtable['title']}</h3>";
			$table->setHeading(array_keys($runtable['tbody'][0]));
			echo $table->generate($runtable['tbody']);
		}
	}
	else {
		$tbody = $export;
		$table->setHeading(array_keys($tbody[0]));
		echo $table->generate($tbody);
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

