<?php 
namespace App\Libraries\Sse\channel;

class teamtime {
	
public $name = 'teamtime';
	
private $db = null;

function __construct() {
	$this->db = \App\Libraries\Sse\Stream::database();
}

function read() {
	// CI not required 
	$sql = "SELECT * FROM `appvars` WHERE `id`='sse.teamtime' LIMIT 1;";
	$result = $this->db->query($sql);
	$row = $result->fetch_object();
	$result->close();
	
	$arr = $row ? json_decode($row->value, 1) : null ;
	if($arr) $arr['updated'] = $row->updated_at;
	else $arr = [];
	return new \App\Libraries\Sse\Event($arr);
}

function write($event) {
	// CI required
	$appvars = new \App\Models\Appvars;
	$appvar = new \App\Entities\Appvar;
	$appvar->id = 'sse.teamtime';
	$appvar->value = $event->__toArray();
	return $appvars->save_var($appvar);
}	
	
}