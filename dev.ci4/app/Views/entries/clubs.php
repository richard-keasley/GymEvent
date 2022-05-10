<?php $this->extend('default');

$this->section('content');
$tbody = [];
foreach($users as $user) {
	$tbody[] = [
		$user->deleted_at ?  
			'<span title="club disabled" class="bi-x-circle text-danger"></span>' : 
			'<span title="club enabled" class="bi-check-circle text-success"></span>',
		$user->name .' ' . $user->link(),
		$user->abbr,
		$user->email,
		$user->entcount
	];
}
$table = new \CodeIgniter\View\Table();
$table->setTemplate(\App\Libraries\Table::templates['default']);
$table->autoHeading = false;
$table->setFooting(['', count($users) . ' clubs', '','', $entcount, '']);
echo $table->generate($tbody);
# d($tbody);

# d($users);
# d($entries);

$this->endSection(); 

$this->section('top'); ?>
<div class="toolbar nav"><?php
echo \App\Libraries\View::back_link("admin/entries/view/{$event->id}");
?></div>
<?php $this->endSection(); 
