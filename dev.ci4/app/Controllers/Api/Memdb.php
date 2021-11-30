<?php namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;

class Memdb extends \App\Controllers\BaseController {
	
use ResponseTrait;

const session_var = 'memdb_connect';
private $connected = false;

function __construct() {
	$this->connected = empty($_SESSION[self::session_var]) ? false : true ;
}
	
public function index() {
	return $this->respondNoContent();
}

public function connect() {
	$key = $this->request->getPost('key');
	$_SESSION[self::session_var] = $key=='sjhb6sibhd9343478scnsd';
	return $_SESSION[self::session_var] ? 
		$this->respond('connected') : 
		$this->failUnauthorized('Invalid credentials');
}

public function set() {
	if(!$this->connected) return $this->failUnauthorized('Not connected');
		
	$appvars = new \App\Models\Appvars();
	foreach(['ethnics', 'groups'] as $varname) {
		$postval = json_decode($this->request->getPost($varname), 1);
		if($postval) {
			$appvar = new \App\Entities\Appvar;
			$appvar->id = "memdb.{$varname}";
			$appvar->value = $postval;
			return $appvars->save_var($appvar) ? 
				$this->respond("{$varname} saved") : 
				$this->fail("Couldn't set {$varname}") ;
		}
	}
	return $this->failNotFound(404, 'no valid variable name found');
}

}
