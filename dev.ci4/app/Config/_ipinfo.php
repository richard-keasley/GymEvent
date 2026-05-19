<?php namespace Config;

use CodeIgniter\Config\BaseConfig;

class _ipinfo extends BaseConfig {

public int $ttl = 432000; // results cached for 5 days
public string $prefix = 'ip_'; // cache prefix

}
