<?php $this->extend('default');
$table = \App\Views\Htm\Table::load('responsive');
$delsure = $delsure ?? null ;

$this->section('content'); ?>
<section>
<h2>Club</h2>
<p><strong>Club:</strong> <?php echo $user->name;?> <?php echo $user->link();?></p>
<p><strong>Email:</strong> <?php echo $user->email;?></p>
<p><strong>Contact name:</strong> <?php echo $clubret->name;?></p>
<p><strong>Address:</strong><br><span class="textarea"><?php echo $clubret->address;?></span></p>
<p><strong>Phone:</strong> <?php echo $clubret->phone;?></p>
<p><strong>Other info:</strong><br><span class="textarea"><?php echo $clubret->other;?></span></p>
<p><strong>Updated:</strong> <?php echo date('d-M-Y H:i', strtotime($clubret->updated));?></p>
</section>

<section>
<h2>Staff</h2>
<?php 
$tbody = []; 
foreach($clubret->staff as $rowkey=>$row) {
	$namestring = new \App\Libraries\Namestring($row['name']);
	$tbody[] = [
		$rowkey + 1,
		humanize($row['cat']),
		$namestring->name,
		$namestring->dob
	];
}
if($tbody) {
	$table->setHeading(['#', '', 'name', 'DoB']);
	echo $table->generate($tbody);
}
echo $clubret->errors('staff'); 
?>
</section>

<section>
<h2>Participants</h2>
<?php 
$tbody = [];
$participants = $clubret->participants;
foreach($participants as $rowkey=>$row) {
	$option = [];
	if($row['opt']) $option[] = humanize($row['opt']);
	if($row['team']) $option[] = $row['team'];
	$option = $option ? sprintf('(%s)', implode(', ', $option)) : '' ;
	foreach($row['names'] as $key=>$name) {
		$namestring = new \App\Libraries\Namestring($name);
		$tbody[] = [
			$key ? '' : $rowkey + 1,
			$key ? '' : $row['dis'],
			$key ? '' : humanize(implode(' ', $row['cat'])),
			$namestring->name,
			$option,
			$namestring->dob
		];
		$option = '';
	}
}
if($tbody) {
	$table->setHeading(['#', 'dis', 'category', 'name', '', 'DoB']);
	echo $table->generate($tbody);
}
echo $clubret->errors('participants'); 

?>
</section>

<?php
$html = $this->include('events/_terms');
if($html) { ?>
<section>
<h2>Terms</h2>
<?php echo $html; ?>
</section>
<?php } ?>

<section>
<h2>Payment</h2>
<div class="textarea"><?php echo $event->payment;?></div>
<?php echo $this->include('clubrets/fees'); ?>
</section>
<?php $this->endSection(); 

$this->section('top'); ?>
<div class="toolbar">
<?php 
echo \App\Libraries\View::back_link($back_link);
echo getlink($clubret->url('edit'), 'edit');
if(isset($users_dialogue)) { ?>
	<button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modalUser" title="Change user for this return"><span class="bi bi-person"></span></button>
	<?php
	echo $this->include('includes/users/dialogue');
}

if($delsure) echo $delsure->button($clubret->id);

?>
</div>

<?php 
if($user->deleted_at) { ?>
<p class="alert alert-danger">This user is not active</p>
<?php } ?>
<?php if($event->deleted_at) { ?>
<p class="alert alert-danger">This event is not listed</p>
<?php } ?>

<?php 
# d($participants);
# d($clubret);
# d($event);

$this->endSection(); 

$this->section('bottom');
if($delsure) echo $delsure->form();
$this->endSection();
