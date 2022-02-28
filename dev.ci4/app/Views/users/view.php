<?php $this->extend('default');
$table = new \CodeIgniter\View\Table();
$template = ['table_open' => '<table class="table compact">'];
$table->setTemplate($template);

$this->section('content'); 
if($user->deleted_at) { ?>
	<p class="alert-danger p-1"><span class="bi bi-x-circle"></span> User disabled</p>
<?php } 

$vartable = new \App\Views\Htm\Vartable;
$tbody = [
	'ID' => [$user->id, null],
	'Name' => [$user->name, null],
	'Short name' => [$user->abbr, null],
	'E-mail' => [$user->email, 'email'],
	'Role' => [$user->role, null],
	'Disabled' => [$user->deleted_at, 'time'],
	'Last active' => [$user->updated, 'time']
];
if(\App\Libraries\Auth::check_role('superuser')) {
	$tbody['cookie'] = [$user->cookie, null];
	$tbody['Reset key'] = [$user->reset_key, null];
	$tbody['Reset requested'] = [$user->reset_time, 'time'];
}
echo $vartable->htm($tbody);

 ?>
<section><h4>Event entries</h4>
<nav class="nav flex-column"><?php 
$nav = [];
$clubrets = $user->clubrets();
foreach($clubrets as $clubret) {
	$event = $clubret->event();
	$links = [];
	if($event->clubrets==1) { // edit
		$links[] = getlink($clubret->url('view'), $event->title);
	}		
	if($event->clubrets==2) { // view	
		$links[] = getlink("entries/view/{$event->id}", $event->title);
		if($event->videos) $links[] = getlink("videos/view/{$event->id}", 'videos');
		if($event->music) $links[] = getlink("music/view/{$event->id}", 'music');
	}
	if($links) $nav[] = implode(' ', $links);
}
foreach($nav as $item) {
	printf('<nav class="nav">%s</nav>', $item);
}
?>
</nav>
</section>

<section><h4>Logins</h4>
<?php $model = new \App\Models\Logins();
$logins = $model->where('user_id', $user->id)->orderBy('updated')->findAll();
$tbody = [];
foreach($logins as $login) {
	$ip_info = \App\Models\Logins::ip_info($login['ip'], ['city', 'countryCode']);
	$tbody[] = [
		'time' => date('d M y H:i', strtotime($login['updated'])),
		'IP' => $login['ip'],
		'location' => implode(', ', $ip_info),
		'result' => $login['error'] ? sprintf('<span class="bg-danger text-light px-1">%s</span>', $login['error']) : '<span class="text-success">OK</span>'
	];
}
if($tbody) {
	$table->setHeading(array_keys($tbody[0]));
	echo $table->generate($tbody);
} ?>
</section>

<?php $this->endSection(); 

$this->section('top');
$attr = [
	'class' => "toolbar sticky-top"
];
echo form_open(base_url(uri_string()), $attr);
echo implode(' ', $toolbar);
echo form_close();

$this->endSection(); 

$this->section('bottom');

if(!empty($show_delUser)) { 
$hidden = ['enable'=>'delete'];
$attr = [
	'id' => "delUser",
	'class' => "modal fade",
	'tabindex' => "-1",
	'aria-hidden' => "true"
];
echo form_open(base_url(uri_string()), $attr, $hidden);
?>
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
	<h5 class="modal-title">Delete user</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
<p>Delete this user and all related data (returns and entries)?</p>
</div>
<div class="modal-footer">
	<button type="submit" class="btn btn-danger">Delete</button>
	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
</div>
</div>
<?php
echo form_close();
}

$this->endSection(); 
