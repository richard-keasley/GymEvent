<?php

namespace App\Exceptions;

use CodeIgniter\Exceptions\FrameworkException;
use CodeIgniter\Exceptions\HTTPExceptionInterface;

class Exception extends FrameworkException implements HTTPExceptionInterface {

public static function exception(string $message='', $code=500) {
	throw new static($message, $code);
}

public static function unauthorized(string $message='Login required') {
	return self::exception($message, 401);
}

public static function forbidden(string $message='Permission denied') {
	return self::exception($message, 403);
}

public static function not_found(string $message='Not found') {
	return self::exception($message, 404);
}

public static function honeypot(string $message='Honeypot full') {
	// remember this IP address;
	model('Logins')->insert(['error' => $message]);
	// uninformative message for user
	return self::exception("I have a bit of an upset tummy!", 422);
}

public static function locked(string $message='Service unavailable') {
	return self::exception($message, 423);
}

static function get_reason($status) {
	$class = new \ReflectionClass('\\CodeIgniter\\HTTP\\Response');
	$reasons = $class->getStaticPropertyValue('statusCodes');
	$reason = $reasons[$status] ?? null ;
	if($reason) return $reason;
	
	$section = floor($status / 100);
	$sections = [
		1 => 'information',
		2 => 'success', 
		3 => 'redirect', 
		4 => 'client error',
		5 => 'server error',
	];
	return $sections[$section] ?? 'undefined error';
}

}
