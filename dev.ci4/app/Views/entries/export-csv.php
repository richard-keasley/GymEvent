<?php 
$discipline_map = [
	'Name' => '',
	'ShortName' => '',
	'Order' => '',
	'filtermaxscore' => 0,
	'multiapparatus' => 1,
	'CategoryId' => '@idCat',
	'agebonus' => 0,
	'dropdown' => 1
];
$category_map = [
	'Name' => '',
	'ShortName' => '',
	'Order' => '',
	'setid' => '',
	'DisId' => '@idDis'
];
$entry_map = [
	'EventId' => '@idEvent',
	'GroupId' => '@idGroup',
	'entrynumber' => '',
	'entrytitle' => '',
	'ClubId' => '',
	'DoB' => '',
	'Guest' => 0,
	'Withdrawn' => 0
];

$tbody = []; $row = [];
foreach($entries as $dis) { 
	$row['dis_name'] = $dis->name;
	$row['dis_abbr'] = $dis->abbr;
	
	foreach($dis->cats as $cat) { 
		$row['cat_name'] = $cat->name;
		$row['cat_abbr'] = $cat->abbr;
		$row['cat_order'] = $cat->sort;
		$row['cat_setid'] = $cat->exercises;
		
		foreach($cat->entries as $entry) {
			$row['entry_club_name'] = $users[$entry->user_id]->name;
			$row['entry_club_shortName'] = $users[$entry->user_id]->abbr;
			$row['entry_number'] = $entry->num;
			$row['entry_title'] = $entry->name;
			$row['entry_DoB'] = $entry->dob;
			$tbody[] = $row;
		}		
		// end cat 
	} // end dis  
} // end event

if(!$row) return; 

$table = new \CodeIgniter\View\Table();
$template = ['table_open' => '<table border=1 style="border-collapse:collapse">'];
$table->setTemplate($template);
$table->setHeading(array_keys($row));
echo $table->generate($tbody);

