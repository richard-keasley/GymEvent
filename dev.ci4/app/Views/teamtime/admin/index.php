<?php $this->extend('default');
use \App\Libraries\Teamtime as tt_lib;

$music_player = tt_lib::get_value("settings", "music_player");

$this->section('sidebar');

$attrs = [
	'class' => "mw-100",
	'style' => "width:21em;",
	'id' => 'runvars'
];
$hidden = ['row' => '', 'col'=>'', 'id'=>''];
echo form_open('', $attrs, $hidden); ?>

<fieldset class="collapse" id="topfields">

<div class="input-group my-1">
<label class="input-group-text">View</label>
<select name="view" class="form-control" onchange="ttrun.set('refresh')"><?php 
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
<?php echo \App\Libraries\View::back_link('teamtime'); ?>

<button type="button" class="btn btn-primary bi bi-arrow-repeat" onclick="ttrun.set('refresh')" title="update displays"></button>

<button class="btn btn-primary" type="button" title="Jump to programme place" data-bs-toggle="modal" data-bs-target="#tt-progjump"><i class="bi-grid-3x3-gap"></i></button>

<button type="button" class="btn bg-info" onclick="$('#btnhelp').click()"><span class="bi bi-question-circle"></span></button>

</span>

<span>

<span class="dropdown">
<button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"><span class="bi bi-list"></span></button>
<ul class="dropdown-menu dropdown-menu-end bg-light">
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
<li><hr class="dropdown-divider"></li>
<li><span class="dropdown-item">
<button type="button" class="btn btn-warning" data-bs-toggle="collapse" data-bs-target="#debug" title="View API replies"><i class="bi-wrench"></i></button>
<button type="button" class="btn btn-warning bi bi-arrow-counterclockwise" onclick="ttrun.set('reload')" title="reload displays"></button>
</span></li>

</ul> 
</span>

</span>

</div>

</fieldset>

<div class="bg-light my-1 p-1 navbar">
<span class="pe-3">
<button type="button" class="btn btn-primary bi bi-arrow-left" onclick="ttrun.set('prev')" title="Previous"></button>
<button type="button" class="btn btn-primary bi bi-arrow-right" onclick="ttrun.set('next')" title="Next"></button>
</span>

<button class="ps-3 btn btn-outline-primary collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#topfields" aria-expanded="false" aria-controls="topfields">
<span class="bi bi-arrows-collapse" title="show settings"></span>
<span class="bi bi-arrows-expand" title="hide settings"></span>
</button>
	
</div>

<div id="msg"></div>

<div class="my-2 p-1" id="run_place">
<h6></h6>
<ul class="this_row"></ul>
<ul class="next_row"></ul>
</div>

<div class="row omode-only my-2 p-1 border">
<div class="col-auto">Timer</div>
<div class="col-auto bg-dark text-light text-center" style="width:4.5em">
	<span class="fw-bold align-middle" id="timertick"></span>
</div>
<div class="col-auto">
	<button type="button" class="btn btn-primary btn-sm bi bi-skip-backward-fill" onclick="ttrun.set('timer0')"></button>
</div>
</div>

<div class="cmode-only my-2 p-1 border"><?php
// [filename, options]
$include = match($music_player) {
	'local' => ['Htm/Playtrack', ['btns' => true]],
	'sender' => ['player/sender', null],
	default => null
};
if($include) echo $this->include($include[0], $include[1]);
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

const ttcontrol = {

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
	ttrun.set('jump');
},

player: {
	name: '<?php echo $music_player;?>',
	play: function(){
		switch(ttcontrol.player.name) {
			case 'local':
			playtrack.play();
			break;
			
			case 'sender':
			ttcontrol.player.sse.play();
			break;
		}
	},
	reset: function() {
		// pause current track
		switch(ttcontrol.player.name) {
			case 'local': 
			playtrack.pause(); 
			break;
			
			case 'sender': 
			ttcontrol.player.sse.pause();	
			break;
		}
		
		if(ttrun.val.mode=='c') {
			// load next track
			var api = [
				ttcontrol.track_api, 
				ttlib.progtable[ttrun.val.row][ttrun.val.col], // entry
				ttlib.progtable[0][ttrun.val.col] // exercise
			].join('/');			
			$.get(api, function(response) {
				var status = response['status'] ?? 'error' ;
				var message = response['message'] ?? null ;
				if(!message) {
					status = 'error';
					message = 'No reply';
				}
				switch(ttcontrol.player.name) {
					case 'local': 
					if(status=='ok') playtrack.load(message, 0); // NB: no autoplay
					else playtrack.message(message);
					break;
					
					case 'sender': 
					// unsupported
					break;
				}
			});
		}
	},
	
	sse: {
		state: 'pause',
		play: function() {
			params = {
				event: <?php echo $event_id;?>,
				num: ttlib.progtable[ttrun.val['row']][ttrun.val['col']],
				exe: ttlib.progtable[0][ttrun.val['col']],
			};
			sse.send('play', params);
			ttcontrol.player.sse.state = 'play';
		},
		pause: function() {
			if(ttcontrol.player.sse.state=='pause') {
				// hide current error (if any)
				sse.message(null, 'pause');
			}
			else {
				// only send pause if we are playing
				sse.send('pause'); 
			}
			ttcontrol.player.sse.state = 'pause';
		}
	}
}

};

const ttrun = {
val: null,

get: function(arr) {
	var keys = ['col','row','timer','timer_current','timer_start'];
	keys.forEach((key) => {
		arr[key] = parseInt(arr[key] ?? 0);
	});
	ttrun.val = arr;
	// console.log(ttrun.val);
	
	keys = ['col','row','timer','message'];
	keys.forEach((key) => {
		var val = ttrun.val[key] ?? '' ;
		$('[name='+key+']').val(val);		
	});
	$('[name=view] option').each(function() {
		this.selected = this.value==ttrun.val['view'];			
	});
	$('#debug pre').html(JSON.stringify(ttrun.val, null, 1));
	
	ttcontrol.message();
	if(ttrun.val.mode=='o') {
		$('.omode-only').show();
		ttlib.timer.init(ttrun.val['timer'], ttrun.val['timer_current']);
	}
	else $('.omode-only').hide();
	
	if(ttrun.val.mode=='c') {
		$('.cmode-only').show(); 
	}
	else $('.cmode-only').hide();
	
	// pause music
	if(ttrun.val.cmd!='refresh' && ttrun.val.cmd!='reload') {
		ttcontrol.player.reset();
	}
		
	$('#run_place h6').html(ttlib.prog_section(ttrun.val['row']));
	var row_num = ttrun.val['row'];
	var prog_row = ttlib.progtable[row_num];
	var html = '';
	var attr = '';
	if(prog_row[0]!='t') {
		prog_row.forEach(function(number, index) {
			if(index) {
				attr = prog_row[0] + 'mode';
				if(index==ttrun.val['col']) attr += ' active';
				html += '<li class="'+attr+'">'+ttlib.team_name(number)+'</li>';
			}
		});
	}		
	$('#run_place .this_row').html(html);

	html = '';
	row_num ++;
	var prog_row = ttlib.progtable[row_num];
	if(typeof prog_row!=='undefined') {
		if(prog_row[0]=='t') {
			html = '<li><em>'+ttlib.prog_section(row_num)+'</em></li>';
		}
		else {
			prog_row.forEach(function(number, index) {
				if(index) html += '<li>'+ttlib.team_name(number)+'</li>';
			});
		}
	}
	$('#run_place .next_row').html(html);
},

set: function(cmd) {
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
		// console.log(response);
		ttrun.get(response);
		if(response.error) ttcontrol.message(response.error);
	})
	.fail(function(jqXHR) {
		ttcontrol.message(get_error(jqXHR)); 
	});
}
};

$(function() {

// set up sender play button
<?php if($music_player=='sender') { ?>
sse.buttons.play.onclick = function() { ttcontrol.player.play(); };
<?php } ?>

var url = '<?php echo site_url("/api/teamtime/get/runvars");?>';
$.get(url, function(response) {
	response.cmd = 'start';
	ttrun.get(response);
})
.fail(function(jqXHR) {
	ttcontrol.message( get_error(jqXHR) ); 
});

var tt = setInterval(function() {
	$('#timertick').html(ttlib.timer.tick(['time']));
}, 1000);

/* 
shortcut keys for buttons
event.altKey 
event.ctrlKey 
*/
$("body").keyup(function(event) {
if(event.ctrlKey) {
	switch(event.key) {
		case 'ArrowRight':
		ttrun.set('next');
		break;

		case 'ArrowLeft':
		ttrun.set('prev');
		break;

		case 'ArrowUp':
		if(ttrun.val.mode=='c') {
			ttcontrol.player.play();
		}
		break;

		case 'ArrowDown':
		ttcontrol.player.reset();
		break;
	}
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
