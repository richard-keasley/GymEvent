<?php namespace Config;

use CodeIgniter\Config\BaseConfig;

class Auth extends BaseConfig {

/* check IP address for errors */
public $errors = [
	'ttl' => 36, // amount of hours to remove records
	'max' => 6  // max number of errors allowed per IP address
];

}
