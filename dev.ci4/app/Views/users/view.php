<?php $this->extend('default');
$template = ['table_open' => '<table class="table table-hover">'];
$table = new \CodeIgniter\View\Table($template);

$this->section('content'); 
if($user->deleted_at) { ?>
	<p class="alert alert-danger"><span class="bi bi-x-circle"></span> User disabled</p>
<?php } 

$vartable = new \App\Views\Htm\Vartable;
$tbody = [
	'ID' => $user->id,
	'Name' => esc($user->name),
	'Short name' => esc($user->abbr),
	'E-mail' => \App\Views\Htm\Table::email($user->email),
	'Role' => $user->role,
	'Disabled' => \App\Views\Htm\Table::time($user->deleted_at),
	'Last active' => \App\Views\Htm\Table::time($user->updated)
];
if(\App\Libraries\Auth::check_role('superuser')) {
	$tbody['Reset key'] = $user->reset_key;
	$tbody['Reset requested'] = \App\Views\Htm\Table::time($user->reset_time);
}
echo $vartable->htm($tbody);

if(!$user->deleted_at) { ?>
<section><h4>Current events</h4>
<nav class="nav flex-column"><?php 
$nav = [];
$clubrets = $user->clubrets();
foreach($clubrets as $clubret) {
	$event = $clubret->event();
	$event_label = \App\Entities\Event::icons['current'] . ' ' . $event->title;
	
	$links = [];
	if($event->clubrets==1) { // edit
		$links[] = getlink($clubret->url('view'), $event_label);
	}		
	if($event->clubrets==2) { // view	
		$links[] = getlink("entries/view/{$event->id}", $event_label);
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
<?php } ?>

<section><h4>Logins</h4>
<?php 
$model = new \App\Models\Logins();
$logins = $model->where('user_id', $user->id)->orderBy('updated')->findAll();
$ipinfo = new \App\Libraries\Ipinfo;
$ip_keys = ['city', 'countryCode'];	
$tbody = [];
foreach($logins as $login) {
	$ip_info = $ipinfo->get($login['ip'])->attributes($ip_keys);

	$IP = $login['ip'];
	$ip_check = $model->check_ip($login['ip']);
	if(!$ip_check) $IP .= ' <i title="blocked" class="bi-x-circle text-danger"></i>';
	
	$tbody[] = [
		'time' => date('d M y H:i', strtotime($login['updated'])),
		'IP' => $IP,
		'location' => implode(', ', $ip_info),
		'result' => $login['error'] ? sprintf('<span class="bg-danger text-light px-1">%s</span>', $login['error']) : '<span class="text-success">OK</span>'
	];
}
if($tbody) {
	$table->setHeading(array_keys($tbody[0]));
	printf('<div class="table-responsive">%s</div>', $table->generate($tbody));
} ?>
</section>

<?php $this->endSection(); 

$this->section('top');
$attr = [
	'class' => "toolbar sticky-top"
];

echo form_open(current_url(), $attr);
echo implode(' ', $toolbar);
echo form_close();
$this->endSection(); 

$this->section('bottom');

if(isset($users_dialogue)) {
	echo $this->include('includes/users/dialogue');
}
if(isset($modal_delete)) {
	echo $this->include('includes/modal_delete');
}

$this->endSection(); 
