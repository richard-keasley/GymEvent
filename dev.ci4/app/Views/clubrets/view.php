<?php $this->extend('default');
$table = new \CodeIgniter\View\Table();

$this->section('content'); ?>
<section>
<h2>Club</h2>
<p><strong>Club:</strong> <?php echo $user->name;?> <?php echo $user->link();?></p>
<p><strong>Email:</strong> <?php echo $user->email;?></p>
<p><strong>Contact name:</strong> <?php echo $clubret->name;?></p>
<p><strong>Address:</strong><br><span class="textarea"><?php echo $clubret->address;?></span></p>
<p><strong>Phone:</strong> <?php echo $clubret->phone;?></p>
<p><strong>Other info:</strong><br><span class="textarea"><?php echo $clubret->other;?></span></p>
</section>

<section>
<h2>Details</h2>
<p><strong>Updated:</strong> <?php echo date('d-M-Y H:i', strtotime($clubret->updated));?></p>

<h5>Staff</h5>
<div class="table-responsive">
<?php 
$template = ['table_open' => '<table class="table">'];
$table->setTemplate($template);
$tbody = []; 
foreach($clubret->staff as $rowkey=>$row) {
	$namestring = new \App\Entities\namestring($row['name']);
	$tbody[] = [
		$rowkey + 1,
		$row['cat'],
		$namestring->name,
		$namestring->bg,
		$namestring->htm_dob()
	];
}
if(count($tbody)) {
	$table->setHeading(['#', '', 'name', 'BG', 'DoB']);
	echo $table->generate($tbody);
}
?>
</div>
<?php echo $clubret->errors('staff'); ?>

<h5>Participants</h5>
<div class="table-responsive">
<?php 
$tbody = [];
$participants = $clubret->participants;
foreach($participants as $rowkey=>$row) {
	$option = [];
	if($row['opt']) $option[] = $row['opt'];
	if($row['team']) $option[] = $row['team'];
	$option = $option ? sprintf('(%s)', implode(', ', $option)) : '' ;
	foreach($row['names'] as $key=>$name) {
		$namestring = new \App\Entities\namestring($name);
		$tbody[] = [
			$key ? '' : $rowkey + 1,
			$key ? '' : $row['dis'],
			$key ? '' : implode(' ', $row['cat']),
			$namestring->name,
			$option,
			$namestring->bg,
			$namestring->htm_dob()
		];
		$option = '';
	}
}
if(count($tbody)) {
	$table->setHeading(['#', 'dis', 'category', 'name', '', 'BG', 'DoB']);
	echo $table->generate($tbody);
}
?>
</div>
<?php echo $clubret->errors('participants'); ?>

</section>

<section>
<h2>Payment</h2>
<div class="textarea"><?php echo $event->payment;?></div>
<?php echo $clubret->fees('htm');?>
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
if(isset($modal_delete)) { ?>
	<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modal_delete" title="Delete this return"><span class="bi bi-trash"></span></button>
	<?php 
	echo $this->include('includes/modal_delete');
}
?>
</div>

<?php 
if($user->deleted_at) { ?>
<p class="alert-danger">This user is not active</p>
<?php } ?>
<?php if($event->deleted_at) { ?>
<p class="alert-danger">This event is not listed</p>
<?php } ?>

<?php 
# d($participants);
# d($clubret);
# d($event);

$this->endSection(); 
