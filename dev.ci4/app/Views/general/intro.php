<?php
$can_edit = \App\Libraries\Auth::check_path('admin/general');
$filepath = FCPATH . 'public/general';
$realpath = realpath($filepath);
$logo_src = ''; 
$files = new \CodeIgniter\Files\FileCollection();
	
if($realpath) {
	$files->addDirectory($filepath);
	// extract logo if it's there
	$pattern = 'logo.';
	foreach($files as $file) {
		if(strpos($file->getPathname(), $pattern)) {
			$logo_src = substr($file->getPathname(), strlen(FCPATH));
		}
	}
	$files->removePattern($pattern);
	if(!$can_edit) $files->removePattern('#\.xls#i');
}

if(!$realpath) {
	printf('<p class="alert alert-danger">Path does not exist. <br><code>%s</code></p>', $filepath);
}

if($logo_src) { ?>
	<div class="item-image">
	<img src="<?php echo base_url($logo_src);?>">
	</div>
<?php } ?>

<p>Discover more about General Gymnastics in the South-east region.</p>
<p>Please tell Kim (Brighton) or Dave (Pegasus) if you see a problem on these pages.</p>

<h4>Downloads</h4>
<?php 
$downloads = new \App\Views\Htm\Downloads($files);
echo $downloads->htm();