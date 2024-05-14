<?php namespace App\Models;
use CodeIgniter\Model;

class Logins extends Model {

protected $table = 'logins';
protected $primaryKey = 'id';
protected $updatedField  = 'updated';
protected $allowedFields = ['ip', 'user_id', 'error', 'updated'];
protected $beforeInsert = ['beforeInsert'];

private $request_ip = null;
protected function initialize() {
	$request = service('request');	
	$this->request_ip = $request->getIPAddress();
}

function beforeInsert($data) {
	if(empty($data['data']['ip'])) $data['data']['ip'] = $this->request_ip;
	return $data;
}

function block_ip($ip) {
	// can't block self
	if($this->request_ip==$ip) return false;
	// check if block exists
	$logins = $this->where('error' , 'blocked')
		->where('ip', $ip)
		->findAll(1);
	if($logins) return false;	
	return $this->insert(['ip' => $ip, 'error' => 'blocked']) ;
}

private $_ip_checks = [];
static $config = null;

function check_ip($ip) {
	if(!self::$config) {
		$config = config('Auth');
		$ttl = intval($config->errors['ttl']);
		if($ttl) {
			self::$config = [
				'TTL' => $ttl . ' hours',
				'del_time' =>  new \CodeIgniter\I18n\Time("-{$ttl} hours"),
				'max' => intval($config->errors['max'])
			];
		}
		else {
			self::$config = [
				'ignore' => 'No IP checks made'
			];
		}
	}
	if(isset(self::$config['ignore'])) return true;	
	
	if(!isset($this->_ip_checks[$ip])) {	
		// delete old records (remove temporary blocks)
		$this->where('updated <', (string) self::$config['del_time'])
			->where('error <>', 'blocked')
			->delete();
				
		// get login errors
		$logins = $this->where('error >' , '')
			->where('ip', $ip)
			->findAll();
				
		$retval = true;
		
		// temporary block
		if(count($logins)>self::$config['max']) $retval = false;
		else {
			foreach($logins as $login) {
				// permanent block
				if($login['error']=='blocked') $retval = false;
			}
		}
		
		$this->_ip_checks[$ip] = $retval;
		# d($this->_ip_checks);
	}
	return $this->_ip_checks[$ip];
}

static function ip_info($ip, $attribs=null) {
	$ip_info = session('ip_info');
	if(!is_array($ip_info)) $ip_info = [];
	if(empty($ip_info[$ip])) {
		$url = "http://ip-api.com/json/{$ip}";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
		$response = curl_exec($ch);
		if($response) $response = json_decode($response, 1);
		if($response) {
			$status = empty($response['status']) ? 'fail' : $response['status'] ;
			if($status!='success') {
				$response['status'] = 'fail';
				if(empty($response['message'])) $response['message'] = 'unknown error'; 
			}
		}
		else {
			#d(curl_getinfo($ch));
			$http_code = intval(curl_getinfo($ch, CURLINFO_HTTP_CODE));
			$message = $http_code>299 ? "Response: {$http_code}" : 'no response' ;
			$response = [
				'status' => 'fail',
				'message' => $message
			];
		}
		curl_close($ch);
		$ip_info[$ip] = $response;
		$session = session();
		$session->set('ip_info', $ip_info);
	}
	$info = $ip_info[$ip];
	
	// complete array requested
	if(empty($attribs)) return $info;

	// just return selected attributes
	$retval = [];
	if($info['status']!='success') {
		// ensure error message is returned
		$retval[] = $info['message'] ?? 'ERROR';
	}
	foreach($attribs as $attrib) {
		if(isset($info[$attrib])) {
			$retval[$attrib] = $info[$attrib];
		}
	}
	return $retval;
}

}