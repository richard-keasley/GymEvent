<div id="tt-progjump" class="modal" tabindex="-1">
<div class="modal-dialog modal-dialog-scrollable">
<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title">Teamtime programme</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body"><?php 
use \App\Libraries\Teamtime as tt_lib;
$progtable = tt_lib::get_value('progtable');

$tbody = []; 
foreach($progtable as $rowkey=>$row) {
	$mode = array_shift($row);
	if(!$rowkey) {
		$colspan = count($row);
		$tr = [[
			'class' => "border-0 text-muted small px-1", 
			'data' => '&nbsp;'
		]];
	}
	else {
		$tr = [[
			'class' => "border-0 text-muted small px-1", 
			'data' => $rowkey
		]];
	}
	
	
	$button = '<button type="button" data-bs-dismiss="modal" class="btn px-4" onclick="ttcontrol.jumpto(%u,%u)">%s</button>';
	foreach($row as $colkey=>$td) {
		if($td!='-' && $rowkey) {
			$td = sprintf($button, $rowkey, $colkey + 1, humanize($td));
		}
			
		$tr[] = match($mode) {
			't' => [
				'class' => "table-info fw-bold",
				'colspan' => $colspan,
				'data' => $td
			],
			
			'o' => [
				'class' => "table-light",
				'data' => $td
			], 
			
			default => [
				'data' => $td
			]
		};
	}
	
	$tbody[] = $tr;
}
$table = \App\Views\Htm\Table::load('fixed');
echo $table->generate($tbody);
?></div>

<div class="modal-footer">
<button type="button" class="btn btn-secondary bi-x-circle-fill" data-bs-dismiss="modal" title="cancel"></button>
</div>

</div>
</div>
</div>