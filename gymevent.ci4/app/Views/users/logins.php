<?php $this->extend('default');
$table = new \CodeIgniter\View\Table();
$template = ['table_open' => '<table class="table compact">'];
$table->setTemplate($template);

$this->section('content');
echo form_open(base_url(uri_string()));
$tbody = [];
$lgn_model = new \App\Models\Logins;
foreach($logins as $login) { 
	$ip_info = \App\Models\Logins::ip_info($login['ip'], ['city', 'countryCode']);
	$tbody[] = [
		date('d M y H:i', strtotime($login['updated'])),
		'IP' => sprintf('<a href="%s/logins/ip/%s">%s</a>', $base_url, $login['ip'], $login['ip']),
		implode(', ', $ip_info),
		sprintf('<a href="%s/logins/user_id/%u">%s</a>', $base_url, $login['user_id'], $login['user_name']),
		$login['error'] ? sprintf('<span class="bg-danger text-light px-1">%s</span>', $login['error']) : '<span class="text-success">OK</span>',
		sprintf('<button class="bi btn btn-danger bi-trash" name="del" value="%u" type="submit"></button>', $login['id']) . sprintf(' <button class="bi btn btn-danger bi-shield-exclamation" name="block" value="%s" type="submit"></button>', $login['ip']) 
	];
}
if($tbody) {
	$table->setHeading(['time', 'IP', 'location', 'user', '', '']);
	echo $table->generate($tbody);
}
?></form>
<?php $this->endSection(); 

