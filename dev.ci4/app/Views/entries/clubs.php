<?php $this->extend('default');
$table = new \CodeIgniter\View\Table();
$template = ['table_open' => '<table class="table">'];
$table->setTemplate($template);

$this->section('content');
$tbody = [];
foreach($users as $user) {
	$tbody[] = [
		$user->deleted_at ?  
			'<span title="club disabled" class="bi-x-circle text-danger"></span>' : 
			'<span title="club enabled" class="bi-check-circle text-success"></span>',
		$user->name,
		$user->abbr,
		$user->email,
		$user->entcount,
		sprintf('<a href="%s" class="bi-eye btn btn-sm btn-outline-secondary"></a>', base_url("admin/users/view/{$user->id}"))
	];
}
$table = new \CodeIgniter\View\Table();
$template = ['table_open' => '<table class="table">'];
$table->setTemplate($template);
$table->autoHeading = false;
$table->setFooting(['', count($users) . ' clubs', '','', $entcount, '']);
echo $table->generate($tbody);
# d($tbody);

# d($users);
#d($entries);

$this->endSection(); 

$this->section('top'); ?>
<div class="toolbar nav"><?php
echo \App\Libraries\View::back_link("admin/entries/view/{$event->id}");
?></div>
<?php $this->endSection(); 
