-- Created <?php echo date('Y-m-d H:i:s');?> 

-- To Do
-- SET CategoryId for all disciplines
-- CREATE EVENT SCORES
-- CREATE EVENT GROUPS
-- RUNNING ORDER (not included)

SET autocommit= 0;
START TRANSACTION;

--   DECLARE EXIT HANDLER FOR SQLEXCEPTION
--     BEGIN
--         ROLLBACK;  -- rollback any changes made in the transaction
--         RESIGNAL;  -- raise again the sql exception to the caller
--     END;
-- 

SET SQL_SAFE_UPDATES = 0;
use scoreboard;

-- INSERT clubs
<?php foreach($users as $user) {
	if($user) {
		$map = [
			'Name' => $user->name,
			'ShortName' => $user->abbr
		];
		printf("INSERT INTO clubs (%s)\n", db_keys($map));
		printf("  SELECT * FROM (SELECT %s) as tmp\n", db_vals($map));
		printf("  WHERE NOT EXISTS (SELECT 1 FROM `clubs` WHERE `Name`=%s);\n", db_val($user->name));
	}
} ?>

-- SET Club IDs
<?php 
$clubvars = [];
foreach($users as $user) {
	$sqlvar = sprintf("@idClub%s", $user->id);
	printf("SET {$sqlvar} = (SELECT ClubId FROM clubs WHERE `Name`=%s);\n", db_val($user->name));
	$clubvars[$user->id] = $sqlvar;
} ?>

-- CREATE EVENT
UPDATE events SET active = 0;
<?php 
$map = [
	'name' => $event->title,
	'startdate' => "{$event->date} 09:00",
	'enddate' => "{$event->date} 18:00",
	'active' => 1
];
printf("INSERT into `events` (%s)\n", db_keys($map));
printf("  VALUES\n  (%s);\n", db_vals($map));
?> 
SET @idEvent = last_insert_id();

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

foreach($entries as $dis) { 
	$discipline_map['Name'] = $dis->name;
	$discipline_map['ShortName'] = $dis->abbr;
	$discipline_map['Order'] = $dis->abbr;
	printf("\n-- CREATE DISCIPLINE: %s\n", $dis->name);
	printf("SET @idCat = %u\n", 1); // No CategoryId set
	printf("INSERT INTO `disciplines` (%s)\n", db_keys($discipline_map));
	printf("  VALUES\n  (%s);\n", db_vals($discipline_map));
	echo "SET @idDis = last_insert_id();\n";
	
	foreach($dis->cats as $cat) { 
		$category_map['Name'] = $cat->name;
		$category_map['ShortName'] = $cat->abbr;
		$category_map['Order'] = $cat->sort;
		$category_map['setid'] = $cat->exercises;
		printf("\n-- CREATE CATEGORY: %s\n", $cat->name);
		printf("INSERT INTO `groups` (%s)\n", db_keys($category_map));
		printf("  VALUES\n  (%s);\n", db_vals($category_map));
				
		printf("INSERT INTO `entrant` (%s)\n", db_keys($entry_map));
		echo "  VALUES\n";

		$arr = $cat->entries;
		end($arr);
		$last = key($arr);
		foreach($arr as $key=>$entry) {
			$entry_map['entrynumber'] = $entry->num;
			$entry_map['entrytitle'] = $entry->name;
			$entry_map['DoB'] = $entry->dob;
			$entry_map['ClubId'] = $clubvars[$entry->user_id] ?? 0;
			printf("  (%s)%s\n", db_vals($entry_map), $key==$last ? ';' : ',');
		}		
		echo "\n";// end cat 
	} // end dis  
} // end event
?>

-- CREATE EVENT SCORES
/* ToDo */

-- ROLLBACK
-- COMMIT

-- SET autocommit = 1;

<?php 
function db_val($value) {
	static $db = null;
	if(!$db) $db = \Config\Database::connect();
	return $db->escape($value);
}

function db_vals($array) {
	// return an string of field values
	$vals = [];
	foreach($array as $val) {
		// preserve variable names (hope there's no values that match)
		if(!preg_match('/\@id[A-Z][a-z]/', $val)) $val =  db_val($val);
		$vals[] = $val;
	}
	return sprintf("%s", implode(", ", $vals));
}

function db_keys($array) {
	// return an string of field names
	return sprintf("`%s`", implode('`, `', array_keys($array)));
}
