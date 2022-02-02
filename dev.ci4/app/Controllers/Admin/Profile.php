<?php namespace App\Controllers\Admin;

class Profile extends \App\Controllers\BaseController {

public function index() {
	$this->data['breadcrumbs'][] = 'admin';
	$this->data['breadcrumbs'][] = 'admin/profile';
	$this->data['title'] = 'Profile images';
	$this->data['head'] = '<style>
body {
	background-image: url(/app/profile/desktop.jpg);
	background-position: center top;
	background-size: 100%;
	background-repeat: no-repeat;
}
main {
	min-height: 32vw;
}
.nav {
	flex-wrap: wrap;
}
.nav img {
	border: 1px solid #633;
	margin: .5em;
	max-width: 20em;
	display: block;
	background: rgba(200,200,200,.5);
}
</style>';
	
	return view('admin/profile', $this->data);
}
}
