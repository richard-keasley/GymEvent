<?php

namespace App\Exceptions;

use \CodeIgniter\Exceptions;

class Exception 
	extends \RuntimeException 
	implements Exceptions\ExceptionInterface, Exceptions\HTTPExceptionInterface 
	{

use Exceptions\DebugTraceableTrait;

# protected $code = 400;

public static function exception(string $message='Application error', $code=500) {
	switch($code) {
		case 401: return self::unauthorized($message);
		case 403: return self::forbidden($message);
		case 404: return self::not_found($message);
		case 423: return self::locked($message);
	}
	return new static($message, $code);
}

public static function unauthorized(string $message=null) {
	if(!$message) $message = 'Login required';
	return new static($message, 401);
}

public static function forbidden(string $message=null) {
	if(!$message) $message = 'Permission denied';
	return new static($message, 403);
}

public static function not_found(string $message=null) {
	if(!$message) $message = 'Not found';
	return new static($message, 404);
}

public static function locked(string $message=null) {
	if(!$message) $message = 'Service unavailable';
	return new static($message, 423);
}

}
