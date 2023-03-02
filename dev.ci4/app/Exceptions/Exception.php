<?php

namespace App\Exceptions;

use \CodeIgniter\Exceptions;

class Exception 
	extends \RuntimeException 
	implements Exceptions\ExceptionInterface, Exceptions\HTTPExceptionInterface 
	{

use Exceptions\DebugTraceableTrait;

public static function exception(string $message='', $code=500) {
	if(!$message) {
		$message = match($code) {
			401 => 'Login required',
			403 => 'Permission denied',
			404 => 'Not found',
			423 => 'Service unavailable',
			default => 'Application error'
		};
	}
	return new static($message, $code);
}

public static function unauthorized(string $message='') {
	return self::exception($message, 401);
}

public static function forbidden(string $message='') {
	return self::exception($message, 403);
}

public static function not_found(string $message='') {
	return self::exception($message, 404);
}

public static function locked(string $message='') {
	return self::exception($message, 423);
}

}
