<?php $this->extend('default');
$tblview = new \CodeIgniter\View\Table();

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
	$tr[$exe] = $track->view();
	if(!$empty && !$track->filename()) $empty = $track->exe;
	$exe_opts[$exe] = $exe;
}
$tbody[] = $tr;

$template = ['table_open' => '<table class="table">'];
$tblview->setTemplate($template);
$tblview->setHeading($exe_opts);
echo $tblview->generate($tbody);

echo form_open_multipart(base_url(uri_string()));
echo form_hidden('back_link', $back_link);
echo form_hidden('upload', 1);
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
<p>Ensure music is in a supported format (<?php echo implode(', ', \App\Libraries\Track::exts_allowed);?>) and smaller than <?php echo formatBytes(\App\Libraries\Track::max_filesize);?>.</p>
</fieldset>

<script>
var uploadButton =  document.querySelector('#btnupload');
uploadButton.addEventListener("click", function() {
	this.disabled = true;
	this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" ></span> wait&hellip;';
});
//*/
</script>
<div class="toolbar"><?php
echo \App\Libraries\View::back_link($back_link);
?></div>
</form>
<?php $this->endSection(); 
