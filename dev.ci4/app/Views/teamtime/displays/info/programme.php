<div class="progtable">
<?php 
$tt_lib = new \App\Libraries\Teamtime;
$get_var = $tt_lib::get_var('progtable');
$progtable = $get_var->value;

if($$progtable) { ?>
<table class="table text-center" style="table-layout:fixed;">
<?php
$key = 0; // in case there's no body
foreach($$progtable as $key=>$row) {
	$mode = array_shift($row); // remove mode item from row
	if($key==0) { // thead
		$col_count = count($row);
		printf('<thead><tr><th>%s</th></tr></thead>', implode('</th><th>', $row));
		continue;
	}
	if($key==1) echo '<tbody>'; // tbody start
	$class = 'default';
	switch($mode) {
		case 't': 
			$class = 'info';
			$tr = sprintf('<td class="table-info" colspan="%u">%s</td>', $col_count, humanize($row[0]));
			break;
		case 'o':
			$class = 'light';
		case 'c':
		default:
			$tr = sprintf('<td>%s</td>', implode('</td><td>', $row));
	}
	printf('<tr class="table-%s">%s</tr>', $class, $tr);
} 
if($key) echo '</tbody>'; // $key>0 if there was a body
?>
</table>
<?php } else { ?>
<p class="alert alert-warning">Programme appears to be empty</p>
<?php } ?>
</div>
