<?php $this->extend('default');

$this->section('content'); 
$username = $user->name ?? '??' ;
printf('<h3>%s. %s (%s)</h3>', $entry->num, $entry->name, $username);
printf('<p><em>%s</em></p>', $category->name);

?>
<div id="player"><?php 

$exe_opts = []; $empty = null;
$tbody = []; $tr = []; 

$track = new \App\Libraries\Track();
$track->event_id = $event->id;
$track->entry_num = $entry->num;
foreach($entry->music as $exe=>$check_state) {
	$track->exe = $exe;
	$track->check_state = $check_state;
	$tr[$exe] = $track->playbtn(['date']);
	if(!$empty && !$track->file()) $empty = $track->exe;
	$exe_opts[$exe] = $exe;
}
$tbody[] = $tr;

$table = \App\Views\Htm\Table::load('default');
$table->setHeading($exe_opts);
echo $table->generate($tbody);

?></div>
<?php 

$attrs = ['id' => "upload"];
$hidden = ['cmd' => "upload"];
echo form_open_multipart('', $attrs, $hidden);
?>
<fieldset><legend>Upload new track</legend>
<div class="mb-3 row">
<div class="col-auto"><div class="input-group">
	<label class="input-group-text">Exercise</label> 
	<?php echo form_dropdown('exe', $exe_opts, $empty, 'class="form-control"');?>
</div></div>
<div class="col-auto">
	<input class="form-control" type="file" name="file">
</div>
<div class="col-auto">
	<button class="btn btn-primary" type="submit" id="btnupload">upload</button>
</div>
<div class="col-auto">
	<button class="btn btn-secondary" type="button" data-bs-toggle="modal" data-bs-target="#dlgcopy"> copy </button>
</div>
</div>
<p>Ensure music is in a supported format (<?php echo implode(', ', \App\Libraries\Track::exts_allowed);?>)
and smaller than <?php echo formatBytes(\App\Libraries\Track::$max_filesize);?>.</p>
<p class="bg-secondary bg-opacity-10">Please use <code>MP3</code> if at all possible. Music in this format gives us far less problems than any other.</p> 
</fieldset>

<script>
$('#upload').submit(function() {
	$('#upload button')
		.attr('disabled', 'disabled')
		.html('<span class="spinner-border spinner-border-sm" role="status"></span> wait');
});

$('input[name=file]')[0].onchange = function() {
    $('#upload').submit();
};

</script>
<?php 
echo form_close();

echo $this->include('Htm/Playtrack');

?>
<div class="toolbar"><?php
echo \App\Libraries\View::back_link("/music/view/{$event->id}");
echo getlink("/admin/music/view/{$event->id}", 'admin');
?></div>

<?php 
# d($event);

$this->endSection(); 

$this->section('bottom'); ?>
<div class="modal" tabindex="-1" id="dlgcopy">
<div class="modal-dialog modal-lg">
<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title">Use the same track as another entry</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<?php
$attrs = ['class' => "modal-body"];
$hidden = [
	'cmd' => "copytrack",
	'exe' => "",
	'src' => ""
];
echo form_open('', $attrs, $hidden);
?>
<p>Select track to use for <?php echo $entry->name; ?> (<span class="exe"></span>).</p>
<p id="dlgcopy-msg" class="bg-light">loading... </p>
<div id="dlgcopy-body" class="container"></div>
<?php echo form_close(); ?>
    
</div>
</div>

<template>
<div class="border p-1 my-1 overflow-hidden">
<button class="float-end copyexe btn btn-primary" type="button" onclick="copytrack.submit(this)"></button>
<div class="d-inline-block fst-italic"><span class="copydate"></span>:</div>
<div class="d-inline-block fst-italic"><span class="copyevent"></span></div>
<div class="d-inline-block fw-bold"><span class="copyname"></span></div>
<div class="d-inline-block">(<span class="copynum"></span>)</div>
</div>
</template>

<script>
const copytrack = {
url: '<?php echo base_url("api/music/usertracks/{$user->id}/{$entry->id}"); ?>',

load: function() {
	copytrack.message.show('Loading...');
	var val = $('#upload [name=exe]').val();
	$('#dlgcopy [name=exe]').val(val);
	$('#dlgcopy [class=exe]').text(val);
		
	var jqxhr = $.get(copytrack.url, {})
		.done(function(response) {
			try {
				if(!response.length) throw 'No tracks found';
				
				var body = $('#dlgcopy-body');
				body.html('');
				var text = '';
				var dt = null;
				var dt_opts = {
					day: 'numeric', 
					month: 'short'
				};
								
				response.forEach(function (row, i) {
					var template = $($('#dlgcopy template').html());

					dt = new Date(row.event_date);
					dt = dt.toLocaleDateString("en-GB", dt_opts);
					template.find('.copyname').html(row.entry_name);
					template.find('.copynum').html(row.entry_num);
					template.find('.copyevent').html(row.event_title);
					template.find('.copydate').html(dt);
					
					var $el = template.find('.copyexe');
					$el.text(row.exe);
					$el.attr('data-event', row.event_id);
					$el.attr('data-entry', row.entry_num);
					$el.attr('data-exe', row.exe);
					
					body.append(template);
					
					// console.log(template.html());

				});				
				copytrack.message.show();
			}
			catch(ex) {
				copytrack.message.show(ex);
			}
			
		})
		.fail(function() {
			copytrack.message.show('API error!');
		});
},

submit: function(btn) {
	var dataset = btn.dataset ?? [];
	$('#dlgcopy [name=src]').val(JSON.stringify(dataset));
	$('#dlgcopy form').submit();
},

message: {
	el: $('#dlgcopy #dlgcopy-msg'),
	show: function(msg='') {
		if(msg) {
			copytrack.message.el.text(msg);
			copytrack.message.el.show();
		}
		else copytrack.message.el.hide();
	}
}	
};

const BSdlgcopy = document.getElementById('dlgcopy');
BSdlgcopy.addEventListener('show.bs.modal', event => copytrack.load());
</script>
</div>
<?php $this->endSection();
