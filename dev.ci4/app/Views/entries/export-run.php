<?php 
// sort by running order, discipline, category, number
$rowsort = [
	'order' => SORT_ASC, // from entry->runorder
	'dis.abbr' => SORT_ASC,
	'cat.order' => SORT_ASC,
	'entry.number' => SORT_ASC
];
$success = array_sort_by_multiple_keys($export, $rowsort);

if(!$success) { ?>
<p class="alert alert-danger">There was a problem with the the sorting.</p>
<?php }

$tbody = [];
foreach($export as $row) {
	$tbody[] = [
		'runorder' => implode(', ', $row['run']),
		'dis' => $row['dis']['name'],
		'cat' => $row['cat']['name'],
		'num' => $row['entry']['number'],
		'club' => $row['entry']['club']['shortName'],
		'name' => $row['entry']['title']
	];
}
$headings = ['runorder', 'dis', 'cat'];
$cattable = new \App\Views\Htm\Cattable($headings);
echo $cattable->htm($tbody);
