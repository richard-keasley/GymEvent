<?php $this->extend('default');
use \App\Libraries\Teamtime as tt_lib;

$event_id = tt_lib::get_value("settings", "event_id");
$remote = tt_lib::get_value('settings', 'remote');
$music_player = tt_lib::get_value("settings", "music_player");

$this->section('sidebar');

$attrs = [
	'class' => $remote=='receive' ? "d-none" : "mw-100",
	'style' => "width:21em;",
	'id' => 'runvars'
];
echo form_open('', $attrs); ?>

<fieldset class="collapse" id="topfields">

<div class="input-group my-1">
	<label class="input-group-text">View</label>
	<select name="view" class="form-control" onchange="set_runvars('refresh')"><?php 
	foreach(tt_lib::get_value('views') as $key=>$view) {
		$label = $view ? $view['title'] : 'default' ;
		printf('<option value="%u">%s</option>', $key, $label);
	}
	?></select>
</div>

<div class="input-group my-1">
	<label class="input-group-text">Timer</label>
	<input type="number" name="timer" class="form-control">
</div>

<div class="form-floating my-1">
  <input name="message" class="form-control" id="fldmessage" placeholder="message">
  <label for="fldmessage">Message</label>
</div>

<div class="navbar my-1">

<span>
<button type="button" class="btn btn-primary bi bi-arrow-repeat" onclick="set_runvars('refresh')" title="update view"></button>

<button class="btn btn-primary" type="button" title="Jump to programme place" data-bs-toggle="modal" data-bs-target="#tt-progjump"><i class="bi-grid-3x3-gap"></i></button>
<input type="hidden" name="row" class="form-control">
<input type="hidden" name="col" class="form-control">
</span>

<span>
<?php echo \App\Libraries\View::back_link('teamtime'); ?>


<button class="btn btn-warning" type="button" data-bs-toggle="collapse" data-bs-target="#debug" title="View API replies"><i class="bi-wrench"></i></button>

<button type="button" class="btn btn-warning bi bi-arrow-counterclockwise" onclick="set_runvars('reload')" title="reload displays"></button>

<span class="dropdown">
	<button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"><span class="bi bi-list"></span></button>
	<ul class="dropdown-menu dropdown-menu-end bg-light" aria-labelledby="dropdownMenuButton1">
	<?php
	$methods = get_class_methods('\\App\\Controllers\\Control\\Teamtime');
	$exclude = ['index', '__construct', 'initController'];
	$methods = array_diff($methods, $exclude);
	$query = http_build_query(['bl' => 'control/teamtime']);
	$attrs = ['class' => "dropdown-item"];
	foreach($methods as $method) {
		$link = anchor("control/teamtime/{$method}?{$query}", $method, $attrs);
		echo "<li>{$link}</li>";
	}	
	?>
	</ul> 
</span>	
</span>

</div>

</fieldset>

<div class="bg-light my-1 p-1 navbar">
<span class="pe-3 runnav">
<button type="button" class="btn btn-primary bi bi-caret-left-fill" onclick="set_runvars('prev')" title="Previous"></button>
<button type="button" class="btn btn-primary bi bi-caret-right-fill" onclick="set_runvars('next')" title="Next"></button>
</span>

<button class="ps-3 btn btn-outline-primary collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#topfields" aria-expanded="false" aria-controls="topfields">
<span class="bi bi-arrows-expand" title="show settings"></span>
<span class="bi bi-arrows-collapse" title="hide settings"></span>
</button>
	
</div>

<div id="msg"></div>

<div class="my-2 p-1" id="run_place">
<h6></h6>
<ul class="this_row"></ul>
<ul class="next_row"></ul>
</div>

<div class="row omode-only my-2 p-1 border">
<div class="col-auto py-1">
	Timer: 
	<span class="bg-dark text-light py-1 px-2" style="width: 8em" id="timertick"></span>
</div>
<div class="col-auto">
	<button type="button" class="btn btn-primary btn-sm bi bi-skip-backward-fill" onclick="set_runvars('timer0')"></button>
</div>
</div>

<div class="cmode-only my-2 p-1 border"><?php 
$include = match($music_player) {
	'local' => 'Htm/Playtrack',
	'remote' => 'player/remote',
	default => null
};
if($include) echo $this->include($include);
else echo $music_player;
?></div>

<?php 
echo form_close();
$this->endSection(); 

$this->section('content');
?>
<div class="ratio ratio-16x9">
<iframe src="<?php echo site_url('teamtime/display/0');?>"></iframe>
</div>

<section id="debug" class="collapse">
<pre class="border my-1 p-1"></pre>
</section>
<?php 
$this->endSection(); 

$this->section('bottom'); 
echo $this->include('teamtime/js');
echo $this->include('teamtime/admin/progjump');
?>

<script>
// const csrf_token = '<?php echo csrf_token();?>';
// const csrf_hash = '<?php echo csrf_hash();?>';

const ttcontrol = {

music_player: '<?php echo $music_player;?>',
track_api: '<?php echo base_url("/api/music/track_url/{$event_id}");?>',
event_id: <?php echo $event_id;?>,

message: function(text='', alert='danger') {
	if(text) {
		text = '<p class="alert alert-'+alert+'">' + text + '</p>';
	}
	$('#msg').html(text);
},

jumpto: function(row, col) {
	$('#runvars [name=row]').val(row);
	$('#runvars [name=col]').val(col);
	set_runvars('jump');
}

};

let runvars = null;	
let entry = 0;
let exe = '';
let url = '';

function set_runvars(cmd='') {
var postvar = {cmd:cmd};
var fields = $('#runvars').serializeArray();
jQuery.each(fields, function(i, field) {
	postvar[field.name] = field.value;
});
// console.log(postvar);

// send to control
url = '<?php echo site_url("/api/teamtime/control");?>';
$.post(url, postvar)
.done(function(response) {
	//console.log(response);
	show_runvars(response);
	if(response.error) ttcontrol.message(response.error);
})
.fail(function(jqXHR) {
	ttcontrol.message(get_error(jqXHR)); 
});

};

function show_runvars(arr) {
	runvars = arr;
	runvars['row'] = parseInt(runvars['row']);
	runvars['col'] = parseInt(runvars['col']);
		
	$('#debug pre').html(JSON.stringify(runvars, null, 1));
	
	$('[name=col]').val(runvars['col']);
	$('[name=row]').val(runvars['row']);
	$('[name=timer]').val(runvars['timer']);
	$('[name=message]').val(runvars['message']);
	$('[name=view] option').each(function() {
		this.selected = this.value==runvars['view'];			
	});
	ttcontrol.message();
	if(runvars.mode=='o') {
		$('.omode-only').show();
		timeticker.init(runvars['timer'], runvars['timer_current']);
	}
	else $('.omode-only').hide();
	
	if(runvars.mode=='c') {
		$('.cmode-only').show(); 
	}
	else $('.cmode-only').hide();
	
	if(ttcontrol.music_player=='local' && runvars.cmd!='refresh') {
		playtrack.pause();
		if(runvars.mode=='c') {
			entry = progtable[runvars.row][runvars.col];
			exe = progtable[0][runvars.col];
			url = [ttcontrol.track_api, entry, exe];
			
			$.get(url.join('/'), function(response) {
				// console.log(response);
				playtrack.load(response, 0); // NB: no autoplay
			})
			.fail(function(jqXHR) {
				playtrack.msg(get_error(jqXHR), 'danger');
			});
		}
	}
		
	$('#run_place h6').html(prog_section(runvars['row']));
	var row_num = runvars['row'];
	var prog_row = progtable[row_num];
	var html = '';
	var attr = '';
	if(prog_row[0]!='t') {
		prog_row.forEach(function(number, index) {
			if(index) {
				attr = prog_row[0] + 'mode';
				if(index==runvars['col']) attr += ' active';
				html += '<li class="'+attr+'">'+team_name(number)+'</li>';
			}
		});
	}		
	$('#run_place .this_row').html(html);

	html = '';
	row_num ++;
	var prog_row = progtable[row_num];
	if(typeof prog_row!=='undefined') {
		if(prog_row[0]=='t') {
			html = '<li><em>'+prog_section(row_num)+'</em></li>';
		}
		else {
			prog_row.forEach(function(number, index) {
				if(index) html += '<li>'+team_name(number)+'</li>';
			});
		}
	}
	$('#run_place .next_row').html(html);
}

function team_name(number) {
	if(number==='-') return number;
	var team_name = teams[number];
	if(typeof team_name==='undefined') team_name = '<em>no name</em>';
	return number + '. ' + team_name;
}

$(function() {

url = '<?php echo site_url("/api/teamtime/get/runvars");?>';
$.get(url, function(response) {
	response.cmd = 'reload';
	show_runvars(response);
})
.fail(function(jqXHR) {
	ttcontrol.message( get_error(jqXHR) ); 
});

var tt = setInterval(function(){
	$('#timertick').html(timeticker.tick(['time']));
}, 1000);

// short cut keys for buttons
$("body").keyup(function(event) {
	if(event.ctrlKey && event.altKey) {
		switch(event.key) {
			case 'ArrowRight':
				set_runvars('next');
				break;
			case 'ArrowLeft':
				set_runvars('prev');
				break;
			case 'ArrowUp':
				if(runvars.mode=='c') {
					playtrack.player.trigger('play');
				}
				break;
			case 'ArrowDown':
				playtrack.player.trigger('pause');
				break;
		}
	}
	switch(event.key) {
		case 'Enter': 
			set_runvars('refresh');
			break;
	}
});

// remember collapse state
let adminExpanded = localStorage.getItem('adminExpanded');
if(adminExpanded=='yes') $('#topfields').collapse('show');
$('[data-bs-toggle=collapse]').on('click', function(event) {
	var collapsed = this.classList.contains('collapsed');
	localStorage.setItem('adminExpanded', collapsed ? 'no' : 'yes' );
});	

});
</script>
<?php $this->endSection(); 
