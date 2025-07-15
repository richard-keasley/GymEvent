<?php 
namespace App\Libraries\Sse\channel;

class teamtime extends none {
	
function read() {
	// CI not required 
	$var = $this->getvar();
	if($var) {
		$sseevent = $var['value'];
		$sseevent['updated'] = $var['updated_at'];
	}
	else $sseevent = [];
	
	return new \App\Libraries\Sse\Event($sseevent);
}
	
}