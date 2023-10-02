<?php $this->extend('default');

$items = []; $sort = [];
foreach($tempfiles as $key=>$file) {
	$time = $file->getMTime();
	$sort[] = $time;
	$items[] = [
		'dt' => date("Y-m-d H:i:s", $time) . ' ' . $file->getBasename(),
		'dd' => file_get_contents($file->getPathname())
	];
}
array_multisort($sort, $items);

$this->section('content'); ?>

<p>There are currently <?php echo count($items);?> files stored. Sessions older than 
<mark><?php echo date("Y-m-d H:i:s", $del_time);?></mark>
are considered expired.</p>

<p>There are <?php echo count($debugfiles);?> debug files stored.</p>

<div class="table-responsive">
<dl class="list-unstyled">
<?php foreach($items as $item) {
	echo "<dt>{$item['dt']}</dt>";
	echo "<dd>{$item['dd']}</dd>";
} ?>
</dl>
</div>

<?php 
$this->endSection(); 

$this->section('top'); 
$attrs = ['class' => "toolbar sticky-top"];
echo form_open(current_url(), $attrs);
echo \App\Libraries\View::back_link("setup");
?>
<button type="submit" name="cmd" value="purge" class="btn btn-danger"><i class="bi-trash"></i></button>
<button type="button" class="btn bg-info" data-bs-toggle="modal" data-bs-target="#modalInfo"><i class="bi bi-question-square"></i></button>
<button type="button" class="btn bg-info" data-bs-toggle="modal" data-bs-target="#modalSession"><i class="bi bi-code"></i></button>
<?php 
echo form_close();
$this->endSection(); 

$this->section('bottom'); ?>
<div class="modal fade" id="modalInfo" tabindex="-1" aria-hidden="true">
<div class="modal-dialog modal-dialog-scrollable modal-lg">
<div class="modal-content">
<div class="modal-header">
	<h5 class="modal-title">Session INI values</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body"><?php
$tbody = [];
foreach($inis as $key=>$val) {
	$key = substr($key, 8);
	$tbody[$key] = $val;
}
# d($tbody);
echo new \App\Views\Htm\Vartable($tbody);
?></div>

</div>
</div>
</div>

<div class="modal fade" id="modalSession" tabindex="-1" aria-hidden="true">
<div class="modal-dialog modal-dialog-scrollable modal-lg">
<div class="modal-content">
<div class="modal-header">
	<h5 class="modal-title">Session values</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
<pre><?php
print_r($_SESSION);
?></pre>
</div>

</div>
</div>
</div>
<?php $this->endSection();