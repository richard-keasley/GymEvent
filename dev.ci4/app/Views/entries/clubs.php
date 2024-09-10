<?php $this->extend('default');

$this->section('content'); ?>
<h4>Club summary
<a href="?dl=clubs" class="btn btn-sm btn-secondary" title="Download this spreadsheet"><span class="bi-download"></span></a>
</h4>
<?php
echo form_close();

# d($tbody);
foreach($tbody as $rowkey=>$row) {
	$tbody[$rowkey]['state'] = $row['state'] ? 
		'<span title="club disabled" class="bi-x-circle text-danger"></span>' : 
		'<span title="club enabled" class="bi-check-circle text-success"></span>' ;
	$tbody[$rowkey]['count'] = \App\Views\Htm\Table::number($row['count']);
}
$table = \App\Views\Htm\Table::load('responsive');
$table->autoHeading = false;
$table->setFooting(['', count($tbody) . ' clubs', '', '', $entcount]);
echo $table->generate($tbody);

# d($users);
# d($entries);

$this->endSection(); 

$this->section('top'); ?>
<div class="toolbar nav"><?php
echo \App\Libraries\View::back_link("admin/entries/view/{$event->id}");
?></div>
<?php $this->endSection(); 
