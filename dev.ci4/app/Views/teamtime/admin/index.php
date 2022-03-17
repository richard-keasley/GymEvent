<?php $this->extend('default');
$event_id = $tt_lib::get_var("settings", "event_id");

$this->section('sidebar');
$attr = [
	'id' => "runvars"
];
echo form_open(base_url(uri_string()), $attr); ?>

<fieldset class="collapse" id="topfields">

<div class="input-group my-1">
	<label class="input-group-text">View</label>
	<select name="view" class="form-control" onchange="set_runvars()"><?php 
	$get_var = $tt_lib::get_var('views');
	foreach($get_var->value as $key=>$view) {
		$label = $view ? $view['title'] : 'default' ;
		printf('<option value="%u">%s</option>', $key, $label);
	}
	?></select>
</div>

<div class="input-group my-1">
	<label class="input-group-text">Run place</label>
	<input type="number" name="row" class="form-control">
	<input type="number" name="col" class="form-control">
	<button type="button" class="btn btn-primary bi bi-skip-backward-fill" onclick="set_runvars('restart')" title="Re-start event"></button>
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

<button type="button" class="btn btn-primary bi bi-arrow-repeat" onclick="set_runvars()" title="refresh displays"></button>

<span>
<?php echo \App\Libraries\View::back_link('teamtime'); ?>
<span class="dropdown">
	<button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"><span class="bi bi-list"></span></button>
	<ul class="dropdown-menu dropdown-menu-end bg-light" aria-labelledby="dropdownMenuButton1">
	<?php
	foreach(['displays','programme','teams','settings','appvars'] as $method) {
		printf('<li><a class="dropdown-item" href="%s?bl=control/teamtime">%s</a></li>', base_url("control/teamtime/{$method}"), $method);
	} 
	?>
	</ul> 
</span>	
</span>

</div>

</fieldset>

<div class="bg-light my-1 p-1 navbar">
<span class="pe-3 runnav">
<button type="button" class="btn btn-primary bi bi-caret-left-fill" onclick="set_runvars('prev')" title="Next"></button>
<button type="button" class="btn btn-primary bi bi-caret-right-fill" onclick="set_runvars('next')" title="Previous"></button>
</span>

<button class="ps-3 btn btn-outline-primary collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#topfields" aria-expanded="false" aria-controls="topfields">
<span class="bi bi-arrows-expand"></span>
<span class="bi bi-arrows-collapse"></span>
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

<div class="cmode-only my-2 p-1 border">
<?php 
$music_player = $tt_lib::get_var("settings", "music_player");
if($music_player=='local') { ?>
<div id="playtrack" class="alert-success">
<audio style="width:100%" controls></audio> 
<p class="m-0">ready&hellip;</p>
</div>
<?php }

if($music_player=='remote') { ?>
<div id="remoteplayer">
<?php echo anchor(base_url('control/player/auto'), 'remote player'); ?> 
<button type="button" class="btn btn-sm btn-primary bi bi-play-fill" onclick="remote_music('play')"></button>
<button type="button" class="btn btn-sm btn-primary bi bi-stop-fill" onclick="remote_music('stop')"></button>
<p class="m-0">ready&hellip;</p>
</div>
<script>
const $remoteplayer = $('#remoteplayer')[0];
const $remoteplayer_msg = $('#remoteplayer p')[0];

function remote_music(state) {
	url = '<?php echo base_url("/api/music/set_remote");?>';
	postvar = {
		event: event_id,
		entry: progtable[runvars['row']][runvars['col']],
		exe: progtable[0][runvars['col']],
		state: state
	};
	postvar[csrf_token] = csrf_hash;
	
	// console.log(postvar);
	$.post(url, postvar)
	.done(function(response) {
		$remoteplayer_msg.innerHTML = response.state + ': ' + response.url;
		$remoteplayer.className = 'alert-success';
	})
	.fail(function(jqXHR) {
		$remoteplayer_msg.innerHTML = get_error(jqXHR);
		$remoteplayer.className = 'alert-danger';
	});
}
</script>

<?php } ?>
</div>

<?php 
echo form_close();
$this->endSection(); 

$this->section('content');?>
<iframe id="displayframe" src="/teamtime/display/0"></iframe>
<?php $this->endSection(); 

$this->section('bottom'); 
echo $this->include('teamtime/js');
?>
<script>
const csrf_token = '<?php echo csrf_token();?>';
const csrf_hash = '<?php echo csrf_hash();?>';
const music_player = '<?php echo $music_player;?>';
const event_id = <?php echo $event_id;?>;
let runvars = null;	
let $player = null;
let playermsg = null;
let postvar = null;
let entry = 0;
let exe = '';
let url = '';

function set_runvars(cmd='') {
	postvar = {cmd:cmd};
	var fields = $('#runvars').serializeArray();
	jQuery.each(fields, function(i, field) {
		postvar[field.name] = field.value;
    });
	// console.log(postvar);
	url = '<?php echo base_url("/api/teamtime/control");?>';
	$.post(url, postvar)
	.done(function(response) {
		show_runvars(response);
	})
	.fail(function(jqXHR) {
		$('#msg').html('<p class="alert-danger">' + get_error(jqXHR) + '</p>'); 
	});
};

function show_runvars(arr) {
	runvars = arr;
	runvars['row'] = parseInt(runvars['row']);
	runvars['col'] = parseInt(runvars['col']);
	
	$('[name=col]').val(runvars['col']);
	$('[name=row]').val(runvars['row']);
	$('[name=timer]').val(runvars['timer']);
	$('[name=message]').val(runvars['message']);
	$('[name=view] option').each(function() {
		this.selected = this.value==runvars['view'];			
	});
	$('#msg').html('');
	if(runvars.mode=='o') {
		$('.omode-only').show();
		timeticker.init(runvars['timer'], runvars['timer_current']);
	}
	else $('.omode-only').hide();
	if(runvars.mode=='c') {
		if(music_player=='local') {
			entry = progtable[runvars['row']][runvars['col']];
			exe = progtable[0][runvars['col']];
			url = '<?php echo base_url("/api/music/track/{$event_id}");?>/'+entry+'/'+exe;
			//console.log(url);
			$.get(url, function(response) {
				playermsg.innerHTML = 'Ready&hellip;';
				$playtrack.className = 'alert-success';
				$player[0].src = response;
				$player.show();
			})
			.fail(function(jqXHR) {
				playermsg.innerHTML = get_error(jqXHR);
				$playtrack.className = 'alert-danger';
				$player[0].pause();
				$player.hide();
			});
		}
		$('.cmode-only').show(); 
	}
	else $('.cmode-only').hide();
		
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
	var team_name = teams[number];
	if(typeof team_name==='undefined') team_name = '<em>no name</em>';
	return number + '. ' + team_name;
}

$(function() {
if(music_player=='local') {
$playtrack = $('#playtrack')[0];
$player = $('#playtrack audio');
playermsg = $('#playtrack p')[0];

$player.on("error", function(e) {
	var msg = '' ;
	switch(e.target.error.code) {
		case e.target.error.MEDIA_ERR_ABORTED: msg = 'Download aborted'; break;
		case e.target.error.MEDIA_ERR_NETWORK: msg = 'Network error'; break;
		case e.target.error.MEDIA_ERR_DECODE: msg = 'Decoding error'; break;
		case e.target.error.MEDIA_ERR_SRC_NOT_SUPPORTED: msg = 'No decoder available'; break;
		default: msg = 'An unknown error occurred.';
    }
	msg = '<a href="' + e.target.src + '" title="try to download this track" target="music">'+msg+'</a>';
	playermsg.innerHTML = msg;
	$playtrack.className = 'alert-danger m-0';
});

} // end local music_player

url = '<?php echo base_url("/api/teamtime/get/runvars");?>';
$.get(url, function(response) {
	show_runvars(response);
})
.fail(function(jqXHR) {
	$('#msg').html('<p class="alert-danger">' + get_error(jqXHR) + '</p>'); 
});

var tt = setInterval(function(){
	$('#timertick').html(timeticker.tick(['time']));
}, 1000);

// short cut keys for buttons
$(".runnav").keyup(function(event) {
	switch(event.key) {
		case 'ArrowRight':
		case 'ArrowDown':
		case ' ':
			set_runvars('next');
			break;
		case 'ArrowUp':
		case 'ArrowLeft':
			set_runvars('prev');
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
