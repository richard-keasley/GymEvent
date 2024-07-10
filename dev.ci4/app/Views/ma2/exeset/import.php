<?php $this->extend('default');
$import = []; 

$this->section('content'); 

$attrs = [];
$hidden = [];
echo form_open_multipart('', $attrs, $hidden);
?>
<p>Select the data file from your device. It will be named something like <code>mag_routines.json</code>. Be aware uploading a new data file will replace any routines currently stored.</p>
<fieldset class="mb-3 row">
	<div class="col-auto"><?php 
	echo \App\Libraries\View::back_link("ma2/routine")
	?></div>
	
	<div class="col-auto">
	<input class="form-control" type="file" name="import">
	</div>
	
	<div class="col-auto">
	<button class="btn btn-primary" type="submit">upload</button>
	</div>
</fieldset>
<?php
echo form_close();

$this->endSection(); 

$this->section('bottom');

# d($file);

if($file && $exesets) include __DIR__ . '/import-import.php';

?>
<script><?php
ob_start();
include __DIR__ . '/exesets.js';

echo ob_get_clean();
/*

$minifier = new MatthiasMullie\Minify\JS();
$minifier->add(ob_get_clean());
echo $minifier->minify();
*/
?></script>
<?php $this->endSection(); 
