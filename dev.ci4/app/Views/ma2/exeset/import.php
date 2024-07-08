<pre><?php 
$export = [];
foreach($exesets as $exeset) {
	$export[] = $exeset->export();
}
# echo json_encode($export); die;


$export = [];
foreach($exesets as $exeset) {
	printf('<p>%s / %s</p>', $exeset->name, $exeset->event);
	$export[] = $exeset->export();
}
$attrs = [];
$hidden = ['exesets' => json_encode($export)];
echo form_open_multipart('', $attrs, $hidden);
?>
<input name="import" type="file">
<button type="submit">Upload</button>
<?php
echo form_close();

