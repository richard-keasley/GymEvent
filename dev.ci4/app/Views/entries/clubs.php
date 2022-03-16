<?php $this->extend('default');
$table = new \CodeIgniter\View\Table();
$template = ['table_open' => '<table class="table">'];
$table->setTemplate($template);

$this->section('content');
if(\App\Libraries\Auth::check_path('admin/entries/edit')) {
	$attr = [
		'class' => "toolbar nav sticky-top"
	];
	echo form_open(base_url(uri_string()), $attr);
	echo \App\Libraries\View::back_link("admin/events/view/{$event->id}");
	echo form_close();
} 

$counts = [];
foreach($entries as $dis) { 
	foreach($dis->cats as $cat) {
		foreach($cat->entries as $entry) {
			$key = $entry->user_id;
			if(empty($counts[$key])) $counts[$key] = 0;
			$counts[$key]++;
		}
	}
}

$tbody = [];
foreach($users as $user) {
	$tbody[] = [
		$user->deleted_at ?  
			'<span title="club disabled" class="bi-x-circle text-danger"></span>' : 
			'<span title="club enabled" class="bi-check-circle text-success"></span>',
		$user->name,
		$user->abbr,
		$user->email,
		$counts[$user->id] ?? 0,
		sprintf('<a href="%s" class="bi-eye btn btn-sm btn-outline-secondary"></a>', base_url("admin/users/view/{$user->id}"))
	];
}
$table = new \CodeIgniter\View\Table();
$template = ['table_open' => '<table class="table">'];
$table->setTemplate($template);
$table->autoHeading = false;
$table->setFooting(['', count($users) . ' clubs', '','', array_sum($counts), '']);
echo $table->generate($tbody);



# d($users);
#d($entries);

$this->endSection(); 
