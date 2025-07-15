<?php 
namespace App\Libraries\Sse\channel;

class none {

protected $attrs = [];

function __construct() {
	$reflect = new \ReflectionClass($this);
	$name = strtolower($reflect->getShortName());
	$this->attrs = [
		'name' => $name,
		'db' => \App\Libraries\Sse\Stream::database(),
		'varname' => "sse.{$name}",
	];
}

function __get($key) {
	return $this->attrs[$key] ?? null;
}

function read() {
	// CI not required
	$arr = [
		'comment' => "No channel",
		'retry' => 60
	];
	return new \App\Libraries\Sse\Event($arr);
}

function write($event) {
	// CI required
	$appvar = new \App\Entities\Appvar;
	$appvar->id = $this->varname;
	$value = $event->__toArray();
	if(isset($value['id'])) {
		// id is used to ensure you have the latest event
		// ensure it's a positive integer
		$value['id'] = max(1, (int) $value['id']);
		// don't store huge numbers
		if($value['id'] > 999) $value['id'] = 1;
	}
	$appvar->value = $value;
	return model('Appvars')->save_var($appvar);
}

protected function getvar() {
	// CI not required
	$sql = "SELECT * FROM `appvars` WHERE `id`='{$this->varname}' LIMIT 1;";
	$result = $this->db->query($sql);
	$row = $result->fetch_array();
	$result->close();
	if(!$row) return null;
	
	$row['value'] = json_decode($row['value'], 1);
	return $row;
}
	
}
