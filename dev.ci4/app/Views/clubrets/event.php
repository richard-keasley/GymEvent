<?php $this->extend('default');
$table = \App\Views\Htm\Table::load('responsive');

$this->section('content');?>
<div class="d-flex flex-wrap gap-3 d-print-block mb-1">

<section class="mw-100">
<?php 
$attr = ['class' => ""];
echo form_open(base_url(uri_string()), $attr);
?>
<h4>Club returns
	<button type="submit" name="download" value="summary" class="btn btn-sm btn-secondary" title="Export this table"><i class="bi bi-table"></i></button>
</h4>
<?php echo form_close();

$tbody = []; $tr = []; $thead = []; $tfoot = [];
foreach($summary as $row) {
	if(!$thead) {
		foreach($row as $key=>$val) {
			$thead[$key] = ($key);
			$arr = array_column($summary, $key);
			$tfoot[$key] = match($key) {
				'club' => count($arr),
				'updated' => '',
				'fees' => \App\Views\Htm\Table::money(array_sum($arr)),
				default => \App\Views\Htm\Table::number(array_sum($arr))
			};
		}
	}
	
	foreach($row as $key=>$val) {
		$tr[$key] = match($key) {
			'fees' => \App\Views\Htm\Table::money($val),
			'updated' => \App\Views\Htm\Table::time($val),
			'club' => $val,
			default => \App\Views\Htm\Table::number($val)
		};		
	}
	$tbody[] = $tr;
}

$table->setHeading($thead);
$table->setFooting($tfoot);
echo $table->generate($tbody);
?>
</section>

<section class="mw-100">
<?php echo form_open(base_url(uri_string()), $attr); ?>
<h4>Staff
	<button type="submit" name="download" value="staff" class="btn btn-sm btn-secondary" title="Export this table"><i class="bi bi-table"></i></button>
</h4>
<?php echo form_close();
$table->setHeading(['club', 'type', 'name', 'BG']);
$table->setFooting([]);
echo $table->generate($staff);
?>
</section>

</div>

<?php echo form_open(base_url(uri_string())); ?>
<h4>Participants
	<button type="submit" name="download" value="participants" class="btn btn-sm btn-secondary" title="Export this table"><i class="bi bi-table"></i></button>
</h4>
<?php echo form_close(); ?>

<div class="d-flex flex-wrap gap-3 d-print-block"><?php
	$headings = ['dis', 'cat'];
	$cattable = new \App\Views\Htm\Cattable($headings);
	$cattable->data = $participants;
	echo $cattable->htm();
	# echo $table->generate($event->participants());
?></div>

<?php 
$this->endSection(); 

$this->section('top');
$attr = [
	'class' => "toolbar sticky-top"
];
echo form_open(base_url(uri_string()), $attr);
echo \App\Libraries\View::back_link($back_link); 
if(isset($users_dialogue)) { ?>
	<button type="button" class="btn btn-success bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#modalUser" title="Add new club return to this event"></button>
	<?php
}
echo $this->include('entries/populate/button');
echo form_close();
if(isset($users_dialogue)) { 
	echo $this->include('includes/users/dialogue');
}
$this->endSection(); 

/*
$this->section('sidebar');
$this->endSection(); 
*/

$this->section('bottom'); 
echo $this->include('entries/populate/form');
d($event->participants());
# d($event);

$this->endSection(); 
