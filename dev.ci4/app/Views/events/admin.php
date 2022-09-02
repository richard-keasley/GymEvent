<?php $this->extend('default');
$table = \App\Views\Htm\Table::load('small');

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
	<button type="button" title="Delete this event" class="btn btn-danger bi-trash"data-bs-toggle="modal" data-bs-target="#modal_delete"></button>
<?php } else { ?>
	<button type="submit" name="state" value="hide" title="hide this event" class="btn btn-danger bi-x-circle"></button>
	<?php	
	echo getlink("events/view/{$event->id}", '<span class="bi-eye" title="customer view of this event"></span>');
	if(in_array($event->clubrets, [1, 2, 3])) {
		echo getlink("admin/clubrets/event/{$event->id}", 'returns');
	}
	if(in_array($event->clubrets, [2])) {
		echo $this->include('entries/populate/button');
	}
	if(in_array($event->clubrets, [2, 3])) {
		echo getlink("admin/entries/view/{$event->id}", 'entries');
		echo getlink("admin/entries/export/{$event->id}", 'export');
	}
	echo $event->link('music');
	echo $event->link('player');
	echo $event->link('videos');
} 

echo form_close();
$this->endSection();

$this->section('content'); ?>
<section class="mb-3 row"><?php

foreach($states as $fldname) {
	$state = $event->$fldname;
	$label = \App\Entities\Event::state_label($state);
	$colour = \App\Entities\Event::state_colour($state);
	printf('<div class="col-auto mx-1">%s <span class="badge bg-%s">%s</span></div>', $fldname, $colour, $label);
} 
?></section>

<?php if($event->clubrets==1) { ?>
<section><h4>Returns</h4>
<div class="row">
<?php 
foreach($event->participants() as $dis) { ?>
	<div class="col-auto">
	<?php 
	$tbody = []; $total = 0;
	foreach($dis['cats'] as $cat) { 
		$count = count($cat['entries']);
		$total += $count;
		$tbody[] = [$cat['name'], ['data'=>$count,'class'=>"text-end"]];
	}
	$table->setHeading([$dis['name'], ['data'=>$total,'class'=>"text-end"]]);
	echo $table->generate($tbody);
	?>
	</div>
<?php } ?>
</div>
</section>
<?php } ?>

<?php if($event->clubrets==2) { ?>
<section><h4>Entries</h4> 
<div class="row"><?php 

$base_edit = "/admin/entries/edit/{$event->id}";
foreach($entries as $dis) { ?>
	<div class="col-auto">
	<?php 
	$tbody = []; $total = 0;
	foreach($dis->cats as $cat) { 
		$params = [
			'disid' => $dis->id,
			'catid' =>$cat->id
		];
		$href = base_url($base_edit .'?' . http_build_query($params));
		$label = anchor($href, $cat->name, ['title' => 'Edit category']);

		$count = count($cat->entries);
		$total += $count ;
		$tbody[] = [$label, ['data'=>$count,'class'=>"text-end"]];
	}
	$table->setHeading([$dis->name, ['data'=>$total,'class'=>"text-end"]]);
	echo $table->generate($tbody);
	?>
	</div>
<?php } ?></div>
</section>
<?php } ?>

<?php $this->endSection(); 

$this->section('bottom'); 
echo $this->include('entries/populate/form');
echo $this->include('includes/modal_delete');
# d($event->clubrets);
# d($event);
$this->endSection();
