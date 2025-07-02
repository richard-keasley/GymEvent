<?php 
if(empty($messages)) $messages = [];
$session = \Config\Services::session();
$flashdata = $session->getFlashdata('messages');
if($flashdata) {
	$messages = array_merge($messages, $flashdata);
	$session->setFlashdata('messages', null);
}
$flashdata = $session->getFlashdata('error');
if($flashdata) $messages[] = [$flashdata, 'danger'];
if(!$messages) return;

?>
<div class="messages"><?php 
foreach($messages as $row) {
	if(is_array($row)) {
		$text = $row[0]; $class = $row[1];
	}
	else {
		$text = $row; $class = 'danger';
	}
	$format = '<div class="alert alert-%s alert-dismissible show fade" role="alert">%s<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
	printf($format, $class, $text);
}
?></div>
