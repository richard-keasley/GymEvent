<?php 
namespace App\Libraries\Sse\channel;

class music extends none {

function read() {
	// CI not required 
	$var = $this->getvar();
	$sseevent = $var ? $var['value'] : [];
	return new \App\Libraries\Sse\Event($sseevent);
}
	
}