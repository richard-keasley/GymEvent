<?php 
$scoreboard = new \App\ThirdParty\scoreboard;
$exesets = [];
foreach($scoreboard->get_exesets() as $exeset) {
	$key = $exeset['SetId'];
	$exesets[$key] = $exeset['children'];
}
			
$fp =  fopen('php://output', 'w');
foreach($entries as $dis) { 
	fputcsv($fp, [$dis->name]);
	fputcsv($fp, ['']);
	foreach($dis->cats as $cat) {
		fputcsv($fp, [$cat->name]);
		$exeset = $exesets[$cat->exercises] ?? [] ;
		$scores = [];
		foreach($exeset as $exe) $scores[$exe['ShortName']] = 0;
		$scores['Tot'] = 0;
		$scores['Pos'] = 1;
		
		$thead = ['num', 'name', 'club'];
		foreach($scores as $exename=>$score) $thead[] = $exename;
		fputcsv($fp, $thead);
										
		foreach($cat->entries as $entry) {
			$row = [
				$entry->num,
				$users[$entry->user_id]->abbr ?? '?',
				$entry->name
			];
			foreach($scores as $exename=>$score) $row[] = $score;
			fputcsv($fp, $row);
		}
		fputcsv($fp, ['']);
	}
	fputcsv($fp, ['']);
}
fclose($fp);
