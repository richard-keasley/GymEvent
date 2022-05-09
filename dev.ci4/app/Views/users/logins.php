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
	
	$row = [
		date('d M y H:i', strtotime($login['updated'])),
		sprintf('<a href="%s/logins/ip/%s">%s</a>', $base_url, $login['ip'], $IP),
		$login['ip_info'],
		$user_link,
		$login['error'] ? sprintf('<span class="bg-danger text-light px-1">%s</span>', $login['error']) : '<span class="text-success">OK</span>',
		'btns' => 
			sprintf('<button class="bi btn btn-danger bi-trash" name="del" value="%u" type="submit"></button>', $login['id']) .
			sprintf(' <button class="bi btn btn-danger bi-shield-exclamation" name="block" value="%s" type="submit"></button>', $login['ip'])
	];
		
	$tbody[] = $row;
	
}
if($tbody) { 
	$table = new \CodeIgniter\View\Table();
	$template = [
		'table_open' => '<div class="table-responsive"><table class="table">',
		'table_close' => '</table></div>'
	];
	$table->setTemplate($template);
	$table->setHeading(['time', 'IP', 'location', 'user', '', '']);
	echo $table->generate($tbody);
}

echo form_close();

# d($logins);

$this->endSection(); 
