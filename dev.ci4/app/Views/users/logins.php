<?php $this->extend('default');

$this->section('content');
echo form_open(base_url(uri_string()));
$tbody = [];	

foreach($logins as $login) { 
	if($login['user_id']) {
		$user_link = anchor("{$base_url}/logins/user_id/{$login['user_id']}", $login['user_name']);
		$path = "admin/users/view/{$login['user_id']}";
		$label = sprintf('<i class="bi bi-person text-primary" title="View user %s"></i>', $login['user_name']);
		$user_link .= ' ' . anchor($path, $label);
	}
	else {
		$user_link = sprintf('<span class="text-muted">%s</span>', $login['user_name']);
	}
	$IP = $login['ip'];
	if(!$login['check_ip']) $IP .= ' <i title="blocked" class="bi-x-circle text-danger"></i>';
	
	$tbody[] = [
		date('d M y H:i', strtotime($login['updated'])),
		sprintf('<a href="%s/logins/ip/%s">%s</a>', $base_url, $login['ip'], $IP),
		$login['ip_info'],
		$user_link,
		$login['error'] ? sprintf('<div class="alert alert-danger my-1 p-1">%s</div>', $login['error']) : '<span class="text-success">OK</span>',
		'btns' => 
			sprintf('<button class="bi btn btn-danger bi-trash" name="del" value="%u" type="submit"></button>', $login['id']) .
			sprintf(' <button class="bi btn btn-danger bi-shield-exclamation" name="block" value="%s" type="submit"></button>', $login['ip'])
	];	
}

if($tbody) { 
	$table = \App\Views\Htm\Table::load('responsive');
	$table->setHeading(['time', 'IP', 'location', 'user', '', '']);
	echo $table->generate($tbody);
}

echo form_close();

# d($logins);

$this->endSection(); 
