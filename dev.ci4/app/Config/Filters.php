<?php namespace Config;

use CodeIgniter\Config\BaseConfig;

class Filters extends BaseConfig {
	// Makes reading things below nicer,
	// and simpler to change out script that's used.
	public $aliases = [
		'csrf'     => \CodeIgniter\Filters\CSRF::class,
		'toolbar'  => \CodeIgniter\Filters\DebugToolbar::class,
		'honeypot' => \CodeIgniter\Filters\Honeypot::class,
		'auth'     => \App\Filters\Auth::class
	];

	// Always applied before every request
	/*
	CSRF: list URLs that will accept POST from outside this site 
	
	api/teamtime: CSRF hash only valid once. Multiple requests are sent from admin screen consider re-creating CSRF hash after each request
	https://stackoverflow.com/questions/38502548/codeigniter-csrf-valid-for-only-one-time-ajax-request
	
	*/
	public $globals = [
		'before' => [
			'auth',
			'csrf' => [
				'except' => [
					'api/teamtime/*',
					'api/mag/exeval/*', 
					'general/intention',
					'mag/routine'
				]
			]
		],
		'after' => [
			'toolbar'
			//'honeypot'
		],
	];

	// Works on all of a particular HTTP method
	// (GET, POST, etc) as BEFORE filters only
	//     like: 'post' => ['CSRF', 'throttle'],
	public $methods = [];

	// List filter aliases and any before/after uri patterns
	// that they should run on, like:
	//    'isLoggedIn' => ['before' => ['account/*', 'profiles/*']],
	public $filters = [];
}
