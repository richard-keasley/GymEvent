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

static function init() {
	self::$usr_model = new \App\Models\Users();
	self::$lgn_model = new \App\Models\Logins();
	
	$appvars = new \App\Models\Appvars();
	$disabled = $appvars->get_value('home.disabled');
	if($disabled) self::$disabled = $disabled;
}

static function loginas($user_id, $method='login') {
	$user = self::$usr_model
		->where('id', $user_id)
		->first();
	if(!$user) return false;
	// create session and cookie
	$_SESSION = [];
	self::update_login($user, $method);
	return $user->id;
}

static function update_login($user, $method='login', $cookie=null) {
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
		$_SESSION = [];
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
}  
 
static function login($name, $password) {
	$login = ['error' => 'invalid username'];
	$user = self::$usr_model
		->where('name', $name)
		->first();
	if($user) {
		$login['error'] = 'wrong password';
		$login['user_id'] = $user->id;
		if(password_verify($password, $user->password)) {
			if(self::loginas($user->id, 'login')) {
				$login['error'] = '';
				self::$lgn_model->insert($login);
				return $user->id;
			}
		}
	}
	// fail
	self::logout();
	self::$lgn_model->insert($login);	
	return 0;
}

static function logout() {
	delete_cookie('auth');
	$user_id = session('user_id');
	if($user_id) {
		self::$usr_model->update($user_id, ['cookie' => '']);
	}
	$_SESSION = [];
}

/* roles and permissions */ 
const roles = ['-', 'club', 'admin', 99=>'superuser'];

static public $check_paths = [];

// can path be viewed by current user
static function check_path($path) {
	if(!isset(self::$check_paths[$path])) {
		$role = self::path_role($path);
		$perm = self::check_role($role);
		self::$check_paths[$path] = [$role, $perm];
	}
	return self::$check_paths[$path][1];
}

// can current user act as this role
static function check_role($role) {
	$user_role = session('user_role');
	$user_rank = intval(array_search($user_role, self::roles));
	$check_rank = array_search($role, self::roles);
	if($check_rank===false) $check_rank = 99;
	return $check_rank<=$user_rank;
}

// role required to view this path
static function path_role($path) {
	$segments = array_pad(explode('/', $path), 4, '');
	$controller = $segments[0];
	$method = $segments[1];
	$param1 = intval($segments[2]);
	$param2 = intval($segments[3]);
	$session_user = intval(session('user_id'));
	
	if(!$controller) return self::roles[0]; // home page
	
	if($controller=='setup') return self::roles[99];
	
	if(in_array($controller, self::$disabled)) return 'disabled';
	
	foreach(self::roles as $role) {
		if($controller==$role) return $role;
		if($method==$role) return $role;
	}
		
	if($controller=='user') return 'club';
		
	if($controller=='music') {
		if(!$method) return 'club';
		if($method=='view') {
			$events = new \App\Models\Events();
			$event = $events->find($param1);
			$state = $event ? $event->music : null;
			switch($state) {
				case 1: // edit
				case 2: // view
					return 'club' ;
					break;
				default: // waiting / finished
					return 'admin';
			}
		}
		if($method=='edit') {
			$model = new \App\Models\Entries();
			$entry = $model->find($param1);
			return $entry ? $entry->role($controller, 'edit') : 'none';
		}
	}
				
	if($controller=='videos') {
		if($method=='view') {
			$events = new \App\Models\Events();
			$event = $events->find($param1);
			$state = $event ? $event->videos : null ;
			switch($state) {
				case 1: // edit
					return "club";
				case 2: // view
					return self::roles[0];
				default: // waiting / finished
					return 'admin';
			}
		}
		if($method=='edit') {
			$model = new \App\Models\Entries();
			$entry = $model->find($param1);
			return $entry ? $entry->role($controller, 'edit') : 'none';
		}
	}
	
	if($controller=='entries') {
		if(!$method) return self::roles[0];
		if($method=='view') {
			$events = new \App\Models\Events();
			$event = $events->find($param1);
			$clubrets = $event ? $event->clubrets : 0 ;
			switch($clubrets) {
				case 1: // edit
					return self::roles[99];
				case 2: // view
					return self::roles[0];
				default: // closed
					return self::roles[99];
			}
		}
	}
	
	if($controller=='clubrets') {
		if(!$method) return 'club';
		if($param2 && $param2!==$session_user) return 'admin';
		$events = new \App\Models\Events();
		$event = $events->find($param1);
		$state = $event ? $event->clubrets : null;
		switch($state) {
			case 1: // edit
				return "club";
			case 2: // view
				return 'none';
			default: // closed
				return 'admin';
		}
	}
	
	return self::roles[0];
}

} 
