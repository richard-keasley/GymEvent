<?php $this->extend('default');

$this->section('sidebar');
$nav = [
	['general', 'General'],
	['general/routine', 'Routine sheets'],
];

foreach($def_rules->exes as $exekey=>$exe) {
	$nav[] = ["admin/general/edit/{$exekey}", "{$exe['name']} skills"];
}

$navbar = new \App\Views\Htm\Navbar($nav);
echo $navbar->htm();
$this->endSection(); 

$this->section('content'); 

$attrs = [
	'id' => "editform"
];
$hidden = [
	'save' => '1',
	'cmd' => '',
	'key' => ''
];
echo form_open_multipart(current_url(), $attrs, $hidden); 
?>
<h4>Downloads</h4>
<?php 

$filepath = \App\Libraries\General::filepath;
if(!is_dir($filepath)) {
	printf('<p class="alert alert-danger">Path does not exist. <br><code>%s</code></p>', $filepath);
}

$files = \App\Libraries\General::files();
$downloads = new \App\Views\Htm\Downloads($files);
$downloads->template['item_after'] = ' <button type="button" name="cmd" onclick="delfile(%1$u)" class="ms-3 btn btn-sm btn-danger bi-trash"></button>';
echo $downloads->htm();
?>
<div class="row my-3">
<div class="col-auto">
	<input type="file" name="file" class="form-control">
</div>
<div class="col-auto">
	<button class="btn btn-primary" type="submit" name="cmd" value="upload">upload</button>
</div>
</div>

<script>
function delfile(id) {
	if(!confirm("Sure you want to delete this file?")) return;
	$('#editform [name=cmd]').val('delfile');
	$('#editform [name=key]').val(id);
	$('#editform').submit();
}
</script>
<?php
echo form_close();

$this->endSection(); 