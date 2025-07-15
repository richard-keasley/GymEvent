<?php namespace App\Libraries;

/*
$ipinfo = new \App\Libraries\Ipinfo;
$ipinfo->list(); // all cache entries
$ipinfo->clean(); // clear expired cache
$ipinfo->get($ip); // get info for new IP
echo $ipinfo; // show HTML table for info
$keys = ['city', 'countryCode'];
echo implode(', ', $ipinfo->attributes($keys)); // summary info
*/

class Ipinfo implements \Stringable {
	
const ttl = 86400; // 24 hours
const prefix = 'ip_';
private $cache = null;
private $attrs = []; // ip info array

function __construct() {
	$this->cache = service('cache');
}

function __get($key) {
	return $this->attrs[$key] ?? null ;
}

function __toString() {
	return $this->attrs ? 
		new \App\Views\Htm\Vartable($this->attrs) : 
		'' ;
}

private function _key($ip) {
	return str_replace('.', '_', self::prefix . $ip);
}

function get($ip) {
	if(!$ip) return $this;
	$key = $this->_key($ip);
	$response = $this->cache->get($key);

	// nothing in cache, use API
	if(!$response) {
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
				if(empty($response['message'])) {
					$response['message'] = 'unknown error'; 
				}
			}
		}
		else {
			# d(curl_getinfo($ch));
			$http_code = intval(curl_getinfo($ch, CURLINFO_HTTP_CODE));
			$message = $http_code>299 ? "Response: {$http_code}" : 'no response' ;
			$response = [
				'status' => 'fail',
				'message' => $message
			];
		}
		curl_close($ch);
		# echo "saving {$key} ";
		$this->cache->save($key, $response, self::ttl);
	}
	$this->attrs = $response;
	return $this;
}

function attributes($keys=null) {
	if(empty($this->attrs)) return [];
	// complete array requested
	if(empty($keys)) return $this->attrs;
	
	// just return selected attributes
	$retval = [];
	
	$status = $this->attrs['status'] ?? 'fail';
	if($status!='success') {
		// ensure error message is returned
		$retval['message'] = $this->attrs['message'] ?? 'ERROR';
	}
	foreach($keys as $key) {
		$val = $this->$key;
		if($val) $retval[$key] = $val;
	}
	return $retval;
}

function list() {
	$expiry = time() - self::ttl;
	$list = [];
	foreach($this->cache->getCacheInfo() as $key=>$row) {
		if(strpos($key, self::prefix)!==0) continue;
		$date = $row['date'] ?? 0;
		$list[] = [
			'key' => $key,
			'date' => $date,
			'current' => $date > $expiry
		];
	}
	return $list;
}

function clean() {
	$count = 0;
	foreach($this->list() as $entry) {
		if(!$entry['current']) {
			if($this->delete($entry['key'])) $count++;
		}
	}
	return $count;
}

function delete($key, $ip=false) {
	// convert IP to key
	if($ip) $key = $this->_key($key);
	return $this->cache->delete($key);
}

}