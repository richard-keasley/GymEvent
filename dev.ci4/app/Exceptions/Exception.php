<?php

namespace App\Exceptions;

use \CodeIgniter\Exceptions;

class Exception 
	extends \RuntimeException 
	implements Exceptions\ExceptionInterface, Exceptions\HTTPExceptionInterface 
	{

use Exceptions\DebugTraceableTrait;

public static function exception(string $message='', $code=500) {
	return new static($message, $code);
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
	(new \App\Models\Logins)->insert(['error' => $message]);
	// ensure uninformative message for user
	return self::exception("We're not feeling well!", 422);
}

public static function locked(string $message='Service unavailable') {
	return self::exception($message, 423);
}

}
