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

const ip_time = 'P2D'; // amount of time to remove records
const ip_errors = 6; // max number of errors allowed per IP address
private $_ip_checks = [];
function check_ip($ip) {
	if(!isset($this->_ip_checks[$ip])) {
		// delete old records (remove temporary blocks)
		$dt = new \DateTime(); 
		// check this far back (older ones deleted)
		$dt->sub(new \DateInterval(self::ip_time)); 
		$this->where('updated <', $dt->format('Y-m-d H:i:s'))
			->where('error <>', 'blocked')
			->delete();
				
		// get login errors
		$logins = $this->where('error >' , '')
			->where('ip', $ip)
			->findAll();
				
		$retval = true;
		
		// temporary block
		if(count($logins)>self::ip_errors) $retval = false;
		else {
			foreach($logins as $login) {
				// permanent block
				if($login['error']=='blocked') $retval = false;
			}
		}
		
		// reserved addresses
		if(strpos($ip, '127.')===0) $retval = true;
		# if(strpos($ip, '86.')===0) $retval = true;
			
		$this->_ip_checks[$ip] = $retval;
		# d($this->_ip_checks);
	}
	return $this->_ip_checks[$ip];
}

static $_ip_info = [];
static function ip_info($ip, $attribs=null) {
	if(empty(self::$_ip_info[$ip])) {
		$url = "http://ip-api.com/json/$ip";
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
		self::$_ip_info[$ip] = $response;
	}
	$info = self::$_ip_info[$ip];
	
	if($info['status']!='success') return [$info['message']];
	if(empty($attribs)) return $info;

	$retval = [];
	foreach($attribs as $attrib) {
		if(isset(self::$_ip_info[$ip][$attrib])) {
			$retval[$attrib] = self::$_ip_info[$ip][$attrib];
		}
	}
	return $retval;
}

}