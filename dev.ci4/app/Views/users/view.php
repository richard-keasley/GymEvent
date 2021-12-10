<?php $this->extend('default');
$table = new \CodeIgniter\View\Table();
$template = ['table_open' => '<table class="table compact">'];
$table->setTemplate($template);

$this->section('content'); 
$data_keys = [
	'id' => ['ID'],
	'name' => ['Name'],
	'abbr' => ['Short name'],
	'email' => ['Email'],
	'role' => ['Role'],
	'deleted_at' => ['Disabled', 'time'],
	'updated' => ['Last active', 'time']
];
if(\App\Libraries\Auth::check_role('superuser')) {
	$data_keys['cookie'] = ['cookie'];	$data_keys['reset_key'] = ['Reset key'];	$data_keys['reset_time'] = ['Reset requested', 'time'];
}
$data = [];
foreach($data_keys as $key=>$label) {
	$data[] = array_merge([$user->$key], $label);
}
if($user->deleted_at) { ?>
	<p class="alert-danger p-1"><span class="bi bi-x-circle"></span> User disabled</p>
<?php } 

echo \App\Libraries\View::Vartable($data);
$mdl_clubrets = new \App\Models\Clubrets();
$clubrets = $mdl_clubrets->lookup_all('user_id', $user->id);
if($clubrets) { ?>
<section><h4>Event entries</h4>
<nav class="nav flex-column"><?php 
foreach($clubrets as $clubret) {
	$event = $clubret->event(); ?>
	<nav class="nav"><?php
	if($event->clubrets==1) { // edit
		echo getlink($clubret->url('view'), $event->title);
	}		
	if($event->clubrets==2) { // view	
		echo getlink("entries/view/{$event->id}", $event->title);
		if($event->videos) echo getlink("videos/view/{$event->id}", 'videos');
		if($event->music) echo getlink("music/view/{$event->id}", 'music');
	} ?>
	</nav>
	<?php
} ?>
</nav>
</section>
<?php } ?>

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