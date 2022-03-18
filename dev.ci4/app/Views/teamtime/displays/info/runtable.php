<div class="runtable"><?php 
$tt_lib = new \App\Libraries\Teamtime;
$get_var = $tt_lib::get_var('progtable');
$progtable = $get_var->value;
$get_var = $tt_lib::get_var('runvars');
$runvars = $get_var->value;
$get_var = $tt_lib::get_var('settings');
$settings = $get_var->value;
$run_rows = $settings['run_rows'];
$row_count = count($run_rows);

$thead = $progtable[0];
$row_num = $runvars['row'];
do { 
	if($progtable[$row_num][0]=='t') break;
	$row_num--; 
} while($row_num>0);
$thead[0] = humanize($progtable[$row_num][1]);

$tbody = array_slice($progtable, $runvars['row'], $row_count);
$mode = $tbody[0][0];
?>
<table>
<thead><tr><?php 
foreach($thead as $key=>$val) {
	$format = $key ? '<td>%s</td>' : '<th>%s</th>' ;
	printf($format, $val);
} 
?></tr></thead>
<tbody><?php
foreach($tbody as $row_key=>$tr) {
	$row_mode = $tr[0];
	$row_class = "{$row_mode}mode";
	if(!$row_key) $row_class.= ' active';
	printf('<tr class="%s"><th>%s</th>', $row_class, $run_rows[$row_key]);
	array_shift($tr);
	if($row_mode=='t') {
		$col_span = sprintf('colspan="%u"', count($thead) - 1);
		$tr = [humanize($tr[0])];
	}
	else {
		$col_span = '';
	}
	foreach($tr as $col_key=>$td) {
		$col_key++;
		$col_class = $col_key==$runvars['col'] ? 'class="active"' : '';
		printf('<td %s %s>%s</td>', $col_span, $col_class, $td);
	}
	echo '</tr>';
}
?></tbody>
</table>
<?php if($mode=='o') { ?>

<div id="timertick">
<div class="progbar">&nbsp;</div>
</div>
<script>
var runvars = <?php echo json_encode($runvars);?>;
timeticker.init(runvars['timer'], runvars['timer_current']);
timeticker.custom = ['#C00','#C30',"#C60","#C90","#9C0","#6C0","#3C0",'#0C0'];
var timer_val = [];
var $timer = $('#timertick .progbar')[0];
var tt = setInterval(function() {
	timer_val = timeticker.tick(['%', 'custom', 'raw']);
	$timer.style.width = timer_val[0];
	$timer.style.background = timer_val[1];
	$timer.innerHTML = timer_val[2] ? '&nbsp;' : 'STOP';	
}, 1000);
</script>

<?php }

if($runvars['message']) {
	printf('<p class="message">%s</p>', htmlspecialchars($runvars['message']));
}

?>
</div>




