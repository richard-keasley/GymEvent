<div class="runtable"><?php 
use \App\Libraries\Teamtime as tt_lib;

$progtable = tt_lib::get_value('progtable');
$runvars = tt_lib::get_value('runvars');
$settings = tt_lib::get_value('settings');

$run_rows = $settings['run_rows'] ?? [];
$row_count = count($run_rows);

$thead = $progtable[0];
// find previous title row
$row_num = $runvars['row'];
do { 
	if($progtable[$row_num][0]=='t') break;
	$row_num--; 
} while($row_num>0);
$title = humanize($progtable[$row_num][1]);
// reduce title to fit in table
$title = explode(' ', $title);
if($title) $title[0] = substr($title[0], 0, 1);
$thead[0] = implode(' ', $title);

$tbody = array_slice($progtable, $runvars['row'], $row_count);
$mode = $tbody[0][0] ?? '';
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
<?php if($mode=='o') { 

?>

<div id="timertick">
<div class="progbar">&nbsp;</div>
</div>
<?php echo new \App\Views\js\timer('tttimer'); ?>
<script>
var runvars = <?php echo json_encode($runvars);?>;
var bgcolours = ['#0C0','#3C0',"#6C0","#9C0","#C90","#C60","#C30",'#C00'];
var timerbar = $('#timertick .progbar')[0];

tttimer.duration = runvars.timer * 1000;
var bgmax = bgcolours.length - 1;
var interval = setInterval(function() {
	var pc = tttimer.format('pc');
	var bgindex = Math.min(bgmax, Math.floor(pc / 100 * bgcolours.length));
	timerbar.innerHTML = pc>99 ? 'STOP' : '&nbsp;' ;
	timerbar.style.width = pc+'%';
	timerbar.style.background = bgcolours[bgindex];
	
	// console.log(pc, bgindex);
}, 1000);
tttimer.start(runvars.timer_start * 1000, interval);
</script>

<?php 
}

if($runvars['message']) {
	printf('<p class="message">%s</p>', htmlspecialchars($runvars['message']));
}
?>
</div>
