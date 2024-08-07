<?php $this->extend('default');

$this->section('content'); 
$username = $user->name ?? '??' ;
printf('<h3>%s. %s (%s)</h3>', $entry->num, $entry->name, $username);
printf('<p><em>%s</em></p>', $category->name);

$exe_opts = []; $empty = null;
$tbody = []; $tr = []; 

$track = new \App\Libraries\Track();
$track->event_id = $event->id;
$track->entry_num = $entry->num;
foreach($entry->music as $exe=>$check_state) {
	$track->exe = $exe;
	$track->check_state = $check_state;
	$tr[$exe] = $track->playbtn();
	if(!$empty && !$track->file()) $empty = $track->exe;
	$exe_opts[$exe] = $exe;
}
$tbody[] = $tr;

$table = \App\Views\Htm\Table::load('default');
$table->setHeading($exe_opts);
echo $table->generate($tbody);

$attr = ['id' => "upload"];
$hidden = ['upload' => "1"];
echo form_open_multipart(current_url(), $attr, $hidden);
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
</div>
<p>Ensure music is in a supported format (<?php echo implode(', ', \App\Libraries\Track::exts_allowed);?>)
and smaller than <?php echo formatBytes(\App\Libraries\Track::$max_filesize);?>.</p>
<p class="bg-secondary bg-opacity-10">Please use <code>MP3</code> if at all possible. Music in this format gives us far less problems than any other.</p> 
</fieldset>

<script>
$('#upload').submit(function() {
	$('#upload [type=submit]')
		.attr('disabled', 'disabled')
		.html('<span class="spinner-border spinner-border-sm" role="status"></span> wait');
});
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
