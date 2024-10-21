<?php 
namespace App\Libraries\Sse\channel;

// music player

class music {
	
public $name = 'music';
	
private $db = null;

function __construct() {
	$this->db = \App\Libraries\Sse\Stream::database();
}

function read() {
	// CI not required 
	$sql = "SELECT `value` FROM `appvars` WHERE `id`='sse.music' LIMIT 1;";
	$result = $this->db->query($sql);
	$row = $result->fetch_object();
	$result->close();
	$arr = $row ? json_decode($row->value, 1) : null ;
	if(!$arr) $arr = [];
	return new \App\Libraries\Sse\Event($arr);
}

function write($event) {
	// CI required
	$appvars = new \App\Models\Appvars;
	$appvar = new \App\Entities\Appvar;
	$appvar->id = 'sse.music';
	$appvar->value = $event->__toArray();
	return $appvars->save_var($appvar);
}	
	
}