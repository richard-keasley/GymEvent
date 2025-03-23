<?php $this->extend('default');

$this->section('content'); 

$table = \App\Views\Htm\Table::load('small');
foreach($tables as $data) {
	if($data['tbody']) {
		echo "<h5>{$data['child_table']} without {$data['parent_table']}</h5>";
		$table->setHeading([$data['child_table'], $data['parent_table']]);
		echo $table->generate($data['tbody']);
	}
}

if($kills) {
/*	?>
<ul class="list-unstyled"><?php
foreach($kills as $kill) echo "<li><code>{$kill}</code></li>";
?></ul>
<?php 
*/
$attr = [
	'class' =>"toolbar"
];
$hidden = [
	'cmd' => 'commit'
];
echo form_open(current_url(), $attr, $hidden); ?>
<button type="submit" name="cmd" value="commit" class="btn btn-danger" title="commit these changes"><i class="bi bi-trash"></i></button>
<?php echo form_close();
}

$this->endSection();
