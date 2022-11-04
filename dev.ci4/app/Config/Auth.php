<?php namespace Config;

use CodeIgniter\Config\BaseConfig;

class Auth extends BaseConfig {

/* check IP address for errors */
public $errors = [
	'ttl' => 0, // amount of hours to remove records
	'max' => 0  // max number of errors allowed per IP address
];

}
