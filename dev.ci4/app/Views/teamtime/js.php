<?php use \App\Libraries\Teamtime as tt_lib; ?>
<script>
const ttlib = {
	
progtable: <?php echo json_encode(tt_lib::get_value('progtable'));?>,

prog_section: function(row_num) {
	do { 
		if(ttlib.progtable[row_num][0]=='t') break;
		row_num--; 
	}
	while(row_num>0)
	title = ttlib.progtable[row_num][1].replace('_', ' ');
	return title;
},

teams: <?php 
	$teams = [];
	foreach(tt_lib::get_value('teams') as $row) $teams[$row[0]] = $row[1];
	echo json_encode($teams);
?>,
	
team_name: function(number) {
	if(number==='-') return number;
	var team_name = ttlib.teams[number] ?? null;
	if(!team_name) team_name = '<em>no name</em>';
	return number + '. ' + team_name;
},

};
</script>
