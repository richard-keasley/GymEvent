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

timer: {
	duration: 0,
	current: 0,
	start: 0,
	end: 0,
	custom: [],
	init: function(duration, current=0) {
		this.duration = duration;// * 1000;
		this.current = current; // * 1000;
		this.start = this.getTime() - current;
		this.end = this.start + this.duration;
		// console.log(this);
	},
	getTime: function() {
		var jsDate = new Date();
		return Math.floor(jsDate.getTime() / 1000);
	},
	formatTime: function(seconds) {
		var minutes = Math.floor(seconds/60); 
		seconds = seconds % 60;
		minutes = minutes.toString().length < 2 ? '0' + minutes : minutes;
		seconds = seconds.toString().length < 2 ? '0' + seconds : seconds;
		return minutes + ':' + seconds;
	},
	tick: function(formats=['raw']) {
		var remainder = this.end - this.getTime();
		if(remainder<0) remainder = 0;
		var retval = [];
		formats.forEach(function(format) {
			var val;
			switch(format) {
				case '%':
				val = Math.floor(100 * (1 - remainder / this.duration)) + '%';
				break;

				case 'time':
				val = this.formatTime(remainder);
				break;
				
				case 'custom': 
				var index = Math.floor(this.custom.length * remainder / this.duration);
				val = this.custom[index];
				break;
				
				default:
				val = Math.floor(remainder);
			}
			retval.push(val);
		}.bind(this)); 
		return retval;
	}
},

};
</script>
