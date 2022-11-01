<?php $this->extend('default');
$video = new \App\Libraries\Video;
$video->event_id = $event->id;
$video->entry_num = $entry->num;

$this->section('content'); 
$username = $user->name ?? '??' ;
printf('<h3>%s. %s (%s)</h3>', $entry->num, $entry->name, $username);

printf('<p><em>%s</em></p>', $category->name);
$exe_opts = []; 
$empty = null;

$tbody = []; $tr = []; 
foreach($entry->videos as $exe=>$url) {
	$video->exe = $exe;
	$video->url = $url;
	$tr[$exe] = $video->view();
	if(!$empty && !$video->url) $empty = $video->exe;
	$exe_opts[$exe] = $exe;
}
$tbody[] = $tr;

$table = \App\Views\Htm\Table::load('default');
$table->setHeading($exe_opts);
echo $table->generate($tbody);
?>
<p>Remember; videos you post here will be viewable by everyone who has the link. Please be careful about what you are sharing!</p> 

<?php echo form_open(current_url()); ?>
<fieldset><legend>Video links for this entry</legend>
<p>Copy the "share" link for each video into the relevant box. Click "save", then check your video will play.</p>
<div class="row mb-3"><?php
$input = [
	'class' => "form-control",
	'type' => 'text'
];
foreach($exe_opts as $exe) {
	$input['name'] = $exe;
	$input['value'] = $entry->videos[$exe];
	?>
	<div class="col-auto"><div class="input-group">
		<label class="input-group-text"><?php echo $exe;?></label>
		<?php echo form_input($input);?>
	</div></div>
	<?php
} ?>
</div>

<div class="toolbar">
	<?php echo \App\Libraries\View::back_link("videos/view/{$event->id}?catid=c{$category->id}");?>	
	<button class="btn btn-primary" type="submit" name="save" value="1">save</button>
</div>
</fieldset>
</form>

<?php echo form_open_multipart(current_url()); ?>
<fieldset><legend>Upload new video</legend>
<p>Please only upload videos if you <em>really can't</em> share them (above).</p>

<div class="row">
<div class="col-auto"><div class="input-group">
	<label class="input-group-text">Exercise</label>
	<?php echo form_dropdown('exe', $exe_opts, $empty, 'class="form-control"');?>
</div></div>
<div class="col-auto">
	<input type="file" name="file" class="form-control">
</div>
<div class="col-auto">
	<button class="btn btn-primary" type="submit" name="upload" value="1">upload</button>
</div>
</div>
</fieldset>
</form>
<?php $this->endSection(); 