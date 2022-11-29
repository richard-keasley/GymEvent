<?php $this->extend('default');

$this->section('content'); 
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
?>

<p>There are currently <?php echo count($items);?> files stored. Sessions older than 
<mark><?php echo date("Y-m-d H:i:s", $del_time);?></mark>
are considered expired.</p>

<p>There are <?php echo count($debugfiles);?> debug files stored.</p>

<dl class="list-unstyled">
<?php foreach($items as $item) {
	echo "<dt>{$item['dt']}</dt>";
	echo "<dd>{$item['dd']}</dd>";
} ?>
</dl>
<?php 
$this->endSection(); 

$this->section('top'); 
$attrs = ['class' => "toolbar sticky-top"];
echo form_open(current_url(), $attrs);
echo \App\Libraries\View::back_link("setup");
echo '<button type="submit" name="cmd" value="purge" class="btn btn-danger"><i class="bi-trash"></i></button>';
echo form_close();
$this->endSection(); 