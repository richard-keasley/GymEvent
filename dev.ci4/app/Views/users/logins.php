<?php $this->extend('default');

$this->section('content');
echo form_open(base_url(uri_string()));
$tbody = [];
foreach($logins as $login) { 
	$row = [
		date('d M y H:i', strtotime($login['updated'])),
		sprintf('<a href="%s/logins/ip/%s">%s</a>', $base_url, $login['ip'], $login['ip']),
		$login['ip_info'],
		sprintf('<a href="%s/logins/user_id/%u">%s</a>', $base_url, $login['user_id'], $login['user_name']),
		$login['error'] ? sprintf('<span class="bg-danger text-light px-1">%s</span>', $login['error']) : '<span class="text-success">OK</span>',
		'btns' => sprintf('<button class="bi btn btn-danger bi-trash" name="del" value="%u" type="submit"></button>', $login['id'])
	];
	if(!$login['blocked']) {
		$row['btns'] .= sprintf(' <button class="bi btn btn-danger bi-shield-exclamation" name="block" value="%s" type="submit"></button>', $login['ip']);
	}
		
	$tbody[] = $row;
	
}
if($tbody) { ?>
	<div class="table-responsive">
	<?php
	$table = new \CodeIgniter\View\Table();
	$template = ['table_open' => '<table class="table">'];
	$table->setTemplate($template);
	$table->setHeading(['time', 'IP', 'location', 'user', '', '']);
	echo $table->generate($tbody);
	?>
	</div> 
<?php }

echo form_close();

# d($logins);

$this->endSection(); 
