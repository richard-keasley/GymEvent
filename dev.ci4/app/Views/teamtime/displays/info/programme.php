<?php 
use \App\Libraries\Teamtime as tt_lib;
$progtable = tt_lib::get_value('progtable');
?>
<div class="progtable">
<?php
if($progtable) { 

$tbody = []; 
foreach($progtable as $key=>$row) {
	$mode = array_shift($row);
	if(!$key) {
		$colspan = count($row);
		$tr = [[
			'class' => "border-0 text-muted small px-1", 
			'data' => '&nbsp;'
		]];
	}
	else {
		$tr = [[
			'class' => "border-0 text-muted small px-1", 
			'data' => $key
		]];
	}
	
	switch($mode) {
		case 't':
		foreach($row as $td) {
			$tr[] = [
				'class' => "table-info fw-bold px-3",
				'colspan' => $colspan,
				'data' => humanize($td)
			];
		}
		break;
		
		case 'o':
		foreach($row as $td) {
			$tr[] = [
				'class' => "table-light px-4",
				'data' => $td
			];
		}
		break;
		
		default:
		foreach($row as $td) {
			$tr[] = [
				'class' => "px-4",
				'data' => $td
			];
		}
	}
	
	$tbody[] = $tr;
}
$table = \App\Views\Htm\Table::load('fixed');
echo $table->generate($tbody);

} else { ?>
<p class="alert alert-warning">Programme appears to be empty</p>
<?php } ?>
</div>
