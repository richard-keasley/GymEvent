<?php namespace App\Controllers\Admin;

class Sb extends \App\Controllers\BaseController {

function test() {
// careful here... it's accessing scoreboard. 
die ('disabled'); 


/* You should enable error reporting for mysqli before attempting to make a connection */
\mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$mysqli = new \mysqli('localhost', 'gymevent_admin', 'H.BUt1ggtOwYvmzUG7k86', 'gymevent_scoreboard');
printf("<p>Success... %s</p>", $mysqli->host_info);

/* Set the desired charset after establishing a connection */
$mysqli->set_charset('utf8mb4');

$sql = 'UPDATE `eventscores` SET 
`Score`=0,`StartValue`=NULL,
`E`=0,`D`=0,`A`=0,`Pen`=0,`S1`=0,`E1`=0,`D1`=0,`A1`=0,`Pen1`=0,`S2`=0,`E2`=0,`D2`=0,`A2`=0,`Pen2`=0,
`Passes`=1,
`DidNotCompete`=true,
`HasScore`=true,
`UpdatedBy`=3
WHERE `EventScoreId` = 66';
/* Select queries return a resultset */
$result = $mysqli->query($sql);
var_dump($result);





	
	
	
}
	
}
