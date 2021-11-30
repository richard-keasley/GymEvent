<?php $this->extend('default');
$table = new \CodeIgniter\View\Table();

$this->section('content'); ?>
<section>
<h2>Club</h2>
<p><label>Club:</label> <?php echo $user->name;?></p>
<p><label>Email:</label> <?php echo $user->email;?></p>
<p><label for="name">Contact name:</label> <?php echo $clubret->name;?></p>
<p><label for="address">address:</label><br><span class="textarea"><?php echo $clubret->address;?></span></p>
<p><label for="phone">phone:</label> <?php echo $clubret->phone;?></p>
<p><label for="other">other info:</label><br><span class="textarea"><?php echo $clubret->other;?></span></p>
</section>

<section>
<h2>Details</h2>
<h5>Staff</h5>
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
echo $clubret->errors('staff');
?>

<h5>Participants</h5>
<?php 
$tbody = [];
$participants = $clubret->participants;
foreach($participants as $rowkey=>$row) {
	foreach($row['names'] as $key=>$name) {
		$namestring = new \App\Entities\namestring($name);
		$tbody[] = [
			$key ? '' : $rowkey + 1,
			$key ? '' : $row['dis'],
			$key ? '' : implode(' ', $row['cat']),
			$namestring->name,
			$namestring->bg,
			$namestring->htm_dob()
		];
	}
}
if(count($tbody)) {
	$table->setHeading(['#', 'dis', 'category', 'name', 'BG', 'DoB']);
	echo $table->generate($tbody);
}
echo $clubret->errors('participants');
?>
</section>

<section>
<h2>Payment</h2>
<div class="textarea"><?php echo $event->payment;?></div>
<?php echo $clubret->fees('htm');?>
</section>
<?php $this->endSection(); 

$this->section('top'); ?>
<div class="toolbar sticky-top"><?php 
echo \App\Libraries\View::back_link($back_link);
echo getlink($clubret->url('edit'), 'edit'); 
?></div>

<?php $this->endSection(); 
