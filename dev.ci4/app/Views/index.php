<?php $this->extend('default');

$this->section('content'); ?>
<p>The Gym Event system deals with all aspects of a gymnastics event. It can be accessed using any modern browser on devices such as smart phones, tablets, lap-tops, PCs.</p>
<p>Please ask Richard or Kevin if you have any questions.</p>
<p class="text-center"><img src="/app/profile/image.png"></p>
<?php $this->endSection(); 

$this->section('sidebar');
$nav = [
	['about', 'About us'],
	'events',
	'admin',
	'general',
	['mag', 'MAG'],
	'teamtime',
	'scoreboard',
	['user', 'Your info']
];
$admin = \App\Libraries\Auth::check_path('admin');
if($admin) $nav[] = 'links';

$navbar = new \App\Views\Htm\Navbar($nav);
echo $navbar->htm();

if(session('pwa')==='standalone') {
	echo $this->include('includes/login');
}

$this->endSection(); 
