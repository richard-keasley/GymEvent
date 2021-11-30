<?php namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Setup how the exception handler works.
 *
 * @package Config
 */
class Exceptions extends BaseConfig
{
	/*
	 |--------------------------------------------------------------------------
	 | LOG EXCEPTIONS?
	 |--------------------------------------------------------------------------
	 | If true, then exceptions will be logged
	 | through Services::Log.
	 |
	 | Default: true
	 */
	public $log = true;

	/*
	|--------------------------------------------------------------------------
	| DO NOT LOG STATUS CODES
	|--------------------------------------------------------------------------
	| Any status codes here will NOT be logged if logging is turned on.
	| By default, only 404 (Page Not Found) exceptions are ignored.
		401 - login required
		403 - permission denied
		423 - blocked
	*/
	public $ignoreCodes = [401, 403, 404, 423];

	/*
	|--------------------------------------------------------------------------
	| Error Views Path
	|--------------------------------------------------------------------------
	| This is the path to the directory that contains the 'cli' and 'html'
	| directories that hold the views used to generate errors.
	|
	| Default: APPPATH.'Views/errors'
	*/
	public $errorViewPath = APPPATH . 'Views/errors';
}
