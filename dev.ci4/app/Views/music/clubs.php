<?php $this->extend('default');

$this->section('content'); 
if($tbody) {
	$track_count = 0;
	$tfoot = ['club' => count($tbody) . ' clubs'];
	$thead = ['club' => 'club'];
	
	foreach($state_labels as $state_label) {
		$href = "/admin/music/view/{$event->id}?status={$state_label}";
		$attrs = [
			'style' => "width:4em; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;",
			'title' => $state_label,
			'class' => "d-block"
		];
		$thead[$state_label] = anchor($href, $state_label, $attrs);
		$column = array_column($tbody, $state_label);
		$sum = array_sum($column);
		$track_count += $sum;
		$tfoot[$state_label] = $sum ? sprintf('%u / %u', $sum, count(array_filter($column))) : '' ;
		foreach($tbody as $rowkey=>$row) {
			$val = $row[$state_label];
			$tbody[$rowkey][$state_label] = $val ? \App\Views\Htm\Table::number($val) : '';
		}
	}
	$tfoot['club'] = sprintf('%u tracks / %u clubs', $track_count, count($tbody));
	
	$table = \App\Views\Htm\Table::load('bordered');
	$table->setFooting($tfoot);
	$table->setHeading($thead);
	echo $table->generate($tbody);
}

if($tbody && $status) { // email dialogue
?>
<div class="toolbar">
<button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#email"><i class="bi bi-envelope"></i></button>
</div>
	
<div class="modal fade" id="email" tabindex="-1" aria-hidden="true">
<div class="modal-dialog modal-lg">
<?php
$attrs = ['class' => "modal-content"];
$hidden = ['sendmail' => 1];
$action = uri_string() . "?status={$status}";
echo form_open($action, $attrs, $hidden);
?>

<div class="modal-header">
<h5 class="modal-title">Email</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
	
<div class="modal-body">
<p class="input-group">
	<label class="input-group-text">Subject</label>
	<?php 
	$input = [
		'type' => "text",
		'name' => "subject",
		'value' => $event->title .' - music upload',
		'class' => "form-control"
	];
	echo form_input($input);
	?>
</p>
<?php
$attr = [
	'name' => 'body',
	'value' => $this->include('music/email')
];
$editor = new \App\Views\Htm\Editor($attr);
echo $editor->htm();
?>
</div>

<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
<button type="submit" class="btn btn-primary">Send email</button>
</div>

<?php echo form_close();?>	
</div>
</div>
<?php  
} // email dialogue
$this->endSection(); 

$this->section('top'); ?>
<form method="GET" class="toolbar" id="selform"><?php 
echo \App\Libraries\View::back_link("admin/music/view/{$event->id}"); 
$attr = [
	'name' => "status",
	'selected' => $status, 
	'options' => ['-'] + \App\Libraries\Track::state_labels,
	'class' => "form-control",
	'onchange' => 'this.form.submit();'
];
echo form_dropdown($attr);
?></form>

<?php 
# d($event);
# d($entries);
# d($users);
# d($state_labels);
# d($status);
$this->endSection(); 

