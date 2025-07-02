<?php namespace App\Libraries;

/*
https://fishbowl.pastiche.org/2004/01/19/persistent_login_cookie_best_practice
*/

helper('cookie');

class Auth {
	
static $usr_model = null;
static $lgn_model = null;
static $appinfo = [];
static $disabled = [];
static $min_role = null;

static function init() {
	self::$usr_model = new \App\Models\Users();
	self::$lgn_model = new \App\Models\Logins();
	
	$appvars = new \App\Models\Appvars();
	$appval = $appvars->get_value('home.disabled');
	if($appval) self::$disabled = $appval;
	$appval = $appvars->get_value('home.roles');
	self::$min_role = $appval['min'] ?? self::roles[0];
}

static function login_request($post=[]) {
	// return unchecked user from POST
	if(empty($post['login'])) return null;

	foreach(['name', 'password', 'password2'] as $key) {
		$post[$key] = trim($post[$key] ?? '');
	}
	return new \App\Entities\User($post);	
}

static function loginas($user_id, $method='login') {
	$user = self::$usr_model
		->where('id', $user_id)
		->first();
	if(!$user) return false;
			
	// create session and cookie
	self::update_login($user, $method);
	return $user->id;
}

static function clear_session() {
	foreach(['method', 'user_id', 'user_name', 'user_role'] as $key)
		$_SESSION[$key] = null;
}

static function update_login($user, $method='login', $cookie=null) {
	// record successful login
	$user->cookie = $cookie ? $cookie : md5(rand());
	// 14400 4 hours @ 3600 sec/hr
	set_cookie('auth', sprintf('%s-%s', $user->id, $user->cookie) , 14400);
	$user->updated = date('Y-m-d H:i:s');
	if($user->hasChanged()) self::$usr_model->save($user);
	$_SESSION['method'] = $method;
	foreach(['id', 'name', 'role'] as $key) $_SESSION["user_{$key}"] = $user->$key;
}

static function check_login() { 
	// check for existing login
	$user_id = session('user_id');
	$user_cookie = get_cookie('auth');
	$user_cookie = $user_cookie ? explode('-', $user_cookie, 2) : [];
	if(count($user_cookie)!=2) $user_cookie = null;
	
	if($user_id) { // try session
		$user = self::$usr_model
			->where('id', $user_id)
			->first();
		if($user) {
			$cookie = $user_cookie ? $user_cookie[1] : null;
			self::update_login($user, 'session', $cookie);
			return;
		}
		self::clear_session(); // invalid user
	}
	if($user_cookie) { // try cookie
		$user_id = $user_cookie[0];
		$user = self::$usr_model
			->where('id', $user_id)
			->where('cookie', $user_cookie[1])
			->first();
		if($user) {
			self::loginas($user_id, 'cookie');
			return;
		}
		delete_cookie('auth');
	}
	// fail
	self::clear_session();
}  
 
static function login($name, $password) {
	$login = [
		'user_id' => 0,
		'error' => 'login error'
	];
			
	$user = self::$usr_model
		->where('name', $name)
		->first();
	if(!$user) {
		$user = self::$usr_model
		->where('email', $name)
		->first();
	}		
			
	if($user) {
		$login['user_id'] = $user->id ;
		if(password_verify($password, $user->password)) {
			if(self::loginas($login['user_id'], 'login')) {
				$login['error'] = ''; // success
			}
			else {
				$login['error'] = "could not login as {$name}";
			}
		}
		else {
			$login['error'] = "wrong password";
		}
	}
	else {
		$login['error'] = "invalid username ({$name})";
	}

	if($login['error']) {
		// fail
		self::logout();
		$request = service('request');
		$login['error'] .= '<br>' . $request->getUri()->getPath();
		$user_id = 0;
	}
	else {
		// success
		$user_id = $login['user_id'];
	}
	
	self::$lgn_model->insert($login);
	return $user_id;
}

static function logout() {
	delete_cookie('auth');
	$user_id = session('user_id');
	if($user_id) {
		self::$usr_model->update($user_id, ['cookie' => '']);
	}
	self::clear_session();
}

/* roles and permissions */ 
const roles = ['-', 'club', 'controller', 'admin', 99=>'superuser'];
const def_role = 'club';

static private $check_paths = [];

static function check_paths() {
	return self::$check_paths;
}

// can path be viewed by current user
static function check_path($path, $index=1) {
	// $index=1 returns permission (bool)
	// $index=0 returns role (string)
	$path = trim($path, "\/\\ ");
	if(!isset(self::$check_paths[$path])) {
		$role = self::path_role($path);
		$perm = self::check_role($role);
		self::$check_paths[$path] = [$role, $perm];
	}
	return self::$check_paths[$path][$index];
}

// can current user act as this role
static function check_role($role, $user_role=null) {
	if(is_null($user_role)) $user_role = session('user_role');
	if(!$role) return true; // no role specified
	$user_rank = intval(array_search($user_role, self::roles));
	$check_rank = array_search($role, self::roles);
	if($check_rank===false) return false;  # $check_rank = 99;
	return $check_rank<=$user_rank;
}

// role required to view this path
static function path_role($path) {
	$path = trim($path, "\/\\ ");
	$segments = array_pad(explode('/', $path), 7, '');
	
	if($segments[0]=='api') array_shift($segments);
	
	$zones = ['user', 'control', 'admin', 'setup'];
	$zone = in_array($segments[0], $zones) ? array_shift($segments) : 'home';
	
	# d($zone, $segments);
		
	$controller = $segments[0];
	$method = $segments[1];
	$param1 = intval($segments[2]);
	$param2 = intval($segments[3]);
	$session_user = intval(session('user_id'));
	
	if(in_array($controller, self::$disabled)) return 'disabled';
	
	switch($zone) {
		case 'user':  return 'club';
		case 'control': return 'controller';
		case 'admin': return 'admin';
		case 'setup': return self::roles[99];
	}
				
	if(!$controller) return self::roles[0]; // home page
	
	
	foreach(self::roles as $role) {
		if($controller==$role) return $role;
		if($method==$role) return $role;
	}
			
	if($controller=='music') {
		switch($method) {
			case 'view':
			$events = new \App\Models\Events;
			$event = $events->find($param1);
			if($event) {
				if($event->private) return 'controller';
				return match(intval($event->music)) {
					\App\Entities\Event::states['edit'] => 'club',
					\App\Entities\Event::states['view'] => 'club',
					default => 'admin'
				};
			}
			break;
				
			case 'edit':
			$entries = new \App\Models\Entries;
			$entry = $entries->find($param1);
			if($entry) {
				$event = $entry->get_event();
				if($event && $event->private) return 'controller';
				return $entry->role($controller, 'edit');
			}
			break;
			
			case 'track_url':
			return self::roles[0];
			
			default:
			return 'club';
		}
	}
		
	if($controller=='entries') {
		switch($method) {
			case 'view':
			$events = new \App\Models\Events;
			$event = $events->find($param1);
			if($event) {
				if($event->private) return 'controller';
				return match(intval($event->clubrets)) {
					\App\Entities\Event::states['edit'] => self::roles[99],
					\App\Entities\Event::states['view'] => self::roles[0],
					default => self::roles[99]
				};
			}
			break;
			
			default:
			return self::roles[0];
		}
	}
	
	if($controller=='clubrets') {
		switch($method) {
			case 'add':
			case 'view':
			case 'edit':
			$events = new \App\Models\Events;
			$event = $events->find($param1);
			if($event) {
				if($event->private) return 'none';
				if($param2 && $param2!==$session_user) return 'admin';
				return match(intval($event->clubrets)) {
					\App\Entities\Event::states['edit'] => "club",
					\App\Entities\Event::states['view'] => 'none',
					default => 'admin'
				};
			}
			break;
			
			default:
			return 'club';
		}
	}
	
	if($controller=='events') {
		switch($method) {
			case 'view':
			$events = new \App\Models\Events;
			$event = $events->find($param1);
			if($event) {
				return $event->private ? 'controller' : self::roles[0];
			}
			break;
		}
	}
		
	return self::roles[0];
}

} 
