<?php $this->extend('default');
$table = \App\Views\Htm\Table::load('small');
$can_edit = \App\Libraries\Auth::check_path("admin/events/edit/{$event->id}");

$this->section('top'); ?>
<h5><?php $date = new \DateTime($event->date); echo $date->format('j F Y');?></h5>

<?php  
# d($event);
# d($event->dates);
# d($event->users('', false));
# d($event->users('clubrets', false));

$attr = [
	'class' => "toolbar nav sticky-top"
];
echo form_open(current_url(), $attr);

echo \App\Libraries\View::back_link('admin/events');

// edit controls
if($can_edit) {
	
echo getlink("admin/events/edit/{$event->id}", 'edit');
$label = '<span class="bi-clipboard-plus" title="Clone this event"></span>';
echo getlink("admin/events/add/{$event->id}", $label);

if($event->deleted_at) { ?>
	<button type="submit" name="state" value="list" title="list this event" class="btn btn-success bi-check-circle"></button>

	<?php if(empty($disk_space['count'])) { ?>
	<button type="button" title="Delete this event" class="btn btn-danger bi-trash" data-bs-toggle="modal" data-bs-target="#modal_delete"></button>
	<?php } ?>

<?php }

else { ?>
	<button type="submit" name="state" value="hide" title="hide this event" class="btn btn-danger bi-x-circle"></button>
<?php }

}

if(!$event->deleted_at) {	 
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

	if($event->link('music')) {
		echo getlink("admin/music/view/{$event->id}", 'music');
	}

	echo $event->link('player');
	echo $event->link('videos');
	echo $event->link('teamtime');
} 

echo form_close();
$this->endSection();

$this->section('content'); ?>
<section class="my-2 row"><?php
foreach($states as $fldname) {
	$state = $event->$fldname;
	$label = \App\Entities\Event::state_label($state);
	$colour = \App\Entities\Event::state_colour($state);
	printf('<div class="col-auto mx-1">%s <span class="badge bg-%s">%s</span></div>', $fldname, $colour, $label);
} 
?></section>

<section class="my-2 row">
<label class="col-auto me-1 fw-bold">Current disk usage:</label>
<?php foreach($disk_space as $key=>$val) { ?>
<div class="col-auto mx-2">
<?php
if($key=='size') $val = formatBytes($val);
printf('%s: %s</div>', humanize($key), $val);
?>
</div>
<?php }
if($disk_space['count'] && $event->deleted_at) { ?>
<div class="col-auto bg-info-subtle">Clear event files to delete this event</div>
<?php } ?>
</section>

<section class="my-2 row">
<ul class="list-unstyled"><?php
$now = new \datetime;
$dates = $event->dates;
asort($dates);
foreach($dates as $key=>$val) {
	if($val) {
		$date = new \datetime($val);
		$format = $date < $now ?
			'<li><em>%s: %s</em></li>' : 
			'<li>%s: %s</li>';
		$key = str_replace('clubrets', 'online entry', $key);
		printf($format, humanize($key), $date->format('j F'));
	}
}
?></ul>
</section>

<?php if($event->clubrets==1) { ?>
<section>
<h4>Returns' summary</h4>
<div class="row">
<?php 
foreach($event->participants() as $dis) { ?>
	<div class="col-auto">
	<?php 
	$tbody = []; $total = 0;
	foreach($dis['cats'] as $cat) { 
		$label = humanize($cat['name']);
		$count = count($cat['entries']);
		$tbody[] = [$label, ['data'=>$count, 'class'=>"text-end"]];
		$total += $count;
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
<section>
<h4>Entries
<a href="?dl=entries" class="btn btn-sm btn-secondary" title="Download this spreadsheet"><span class="bi-download"></span></a>
</h4>

<div class="row"><?php 
foreach($entries as $dis) { ?>
	<div class="col-auto">
	<?php
	$tbody = [];
	$count = 0;
	echo "<h5>{$dis['disname']}</h5>"; 
	foreach($dis['cats'] as $cat) {
		$count += $cat['count'];
		$cat['count'] = \App\Views\Htm\Table::number($cat['count']);
		$tbody[] = $cat;
	}
	$tfoot = [
		'total',
		\App\Views\Htm\Table::number($count)
	];
	
	$table->autoHeading = false;
	$table->setFooting($tfoot);
	echo $table->generate($tbody);
	?>
	</div>
	<?php 
}  
?></div>
</section>
<?php } ?>

<?php $this->endSection(); 

$this->section('bottom');

// edit dialogues
if($can_edit) {
echo $this->include('entries/populate/form');
echo $this->include('includes/modal_delete');
}
// end edit dialogues


# d($event->clubrets);
# d($event);
$this->endSection();
