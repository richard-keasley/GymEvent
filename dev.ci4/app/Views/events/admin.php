<?php $this->extend('default');
$table = new \CodeIgniter\View\Table();
$template = ['table_open' => '<table class="table compact">'];
$table->setTemplate($template);

$this->section('top'); ?>
<h5><?php $date = new DateTime($event->date); echo $date->format('j F Y');?></h5>

<?php 
$attr = [
	'class' => "toolbar nav sticky-top"
];
echo form_open(base_url(uri_string()), $attr);

echo \App\Libraries\View::back_link('admin/events');
echo getlink("admin/events/edit/{$event->id}", 'edit');
if($event->deleted_at) { ?>
	<button type="submit" name="state" value="list" title="list this event" class="btn btn-success bi-check-circle"></button>
	<button type="button" title="Delete this event" class="btn btn-danger bi-trash"data-bs-toggle="modal" data-bs-target="#delentry"></button>
<?php } else { ?>
	<button type="submit" name="state" value="hide" title="hide this event" class="btn btn-danger bi-x-circle"></button>
	<?php	
	echo getlink("events/view/{$event->id}", '<span class="bi-eye" title="customer view of this event"></span>');
	echo getlink("admin/clubrets/event/{$event->id}", 'returns');
	echo getlink("admin/entries/view/{$event->id}", 'entries');
	echo getlink("admin/music/view/{$event->id}", 'music');
	echo getlink("videos/view/{$event->id}", 'videos');
	echo getlink("admin/entries/export/{$event->id}", 'export');
	echo view('entries/populate/button', ['event'=>$event]);
} 

echo form_close();
$this->endSection();

$this->section('content'); ?>
<section class="mb-3 row"><?php

foreach(['clubrets', 'videos', 'music'] as $fldname) {
	$state = $event->$fldname;
	$label = \App\Entities\Event::state_label($state);
	$colour = \App\Entities\Event::state_colour($state);
	printf('<div class="col-auto mx-1">%s <span class="badge bg-%s">%s</span></div>', $fldname, $colour, $label);
} 
?></section>

<section><h4>Returns</h4>
<div class="row">
<?php foreach($event->participants() as $dis) { ?>
	<div class="col-auto">
	<?php 
	$tbody = []; $total = 0;
	foreach($dis['cats'] as $cat) { 
		$count = count($cat['entries']);
		$total += $count;
		$tbody[] = [$cat['name'], $count];
	}
	$table->setHeading([$dis['name'], $total]);
	echo $table->generate($tbody);
	?>
	</div>
<?php } ?>
</div>
</section>

<section><h4>Entries</h4> 
<div class="row"><?php 
foreach($entries as $dis) { ?>
	<div class="col-auto">
	<?php 
	$tbody = []; $total = 0;
	foreach($dis->cats as $cat) { 
		$count = count($cat->entries);
		$total += $count ;
		$tbody[] = [$cat->name, $count];
	}
	$table->setHeading([$dis->name,$total]);
	echo $table->generate($tbody);
	?>
	</div>
<?php } ?></div>
</section>

<?php $this->endSection(); 

$this->section('bottom'); 
echo view('entries/populate/form', ['event'=>$event]);
?>
<div id="delentry" class="modal" tabindex="-1">
<div class="modal-dialog">
<?php
$attr = [
	'class' => "modal-content"
];
echo form_open(base_url(uri_string()), $attr);
?>
<div class="modal-header">
	<h5 class="modal-title">Delete item</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
	<p>Delete this event?</p>
	<p class="alert-primary">Be aware all related entries and club returns will also be deleted.</p>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary bi-x-circle-fill" data-bs-dismiss="modal"></button>
	<button type="submit" name="state" value="delete" class="btn btn-danger bi-trash"></button>
</div>
<?php echo form_close();?>
</div>
</div>

<?php $this->endSection();
