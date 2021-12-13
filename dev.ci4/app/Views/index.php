<?php $this->extend('default');

$this->section('content'); ?>
<p>The Gym Event system deals with all aspects of a gymnastics event. It can be accessed using any modern browser on devices such as smart phones, tablets, lap-tops, PCs.</p>
<p>Please ask Richard or Kevin if you have any questions.</p>
<p class="text-center"><img src="/public/profile/image.png"></p>
<?php $this->endSection(); 

$this->section('sidebar');
$navbar = ['nav' => [
	['about', 'About us'],
	'events',
	'admin',
	'general',
	'mag',
	'teamtime',
	'scoreboard',
	['user', 'Your info']
]];
echo view('includes/navbar', $navbar);
$this->endSection(); 
