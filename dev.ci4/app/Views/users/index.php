<?php $this->extend('default');
$current_url = current_url(true);
$request_page = implode('/', $current_url->getSegments());
parse_str($current_url->getQuery(), $request_query);
$delsure = $delsure ?? null ;

$this->section('content');
# d($request_page, $request_query, $base_url);
?>
<div class="sticky-top">
<div class="table-responsive toolbar">
<div>
	<a class="btn btn-secondary" href="<?php echo $base_url;?>/add">Add user</a>
	<a class="btn btn-outline-secondary" href="<?php echo $base_url;?>/logins">Logins</a>

	<div class="btn-group"><?php

	printf('<a class="btn btn-outline-secondary" href="%s">*</a>', $base_url); 
		
	$query = $request_query;
	$attrs = [
		'class' => "btn btn-outline-secondary"
	];
	foreach(\App\Libraries\Auth::roles as $role) {
		$query['by'] = "role";
		$query['val'] = $role;
		echo anchor($request_page . '?' . http_build_query($query), $role, $attrs);
	}
	
	foreach(['enabled', 'disabled'] as $status=>$label) {
		$query['by'] = "deleted";
		$query['val'] = $status;
		echo anchor($request_page . '?' . http_build_query($query), $label, $attrs);
	}
	?></div>
</div>	
</div>
</div>

<?php
$filter = new \App\Views\Htm\Filter;
echo $filter->htm();

$table = \App\Views\Htm\Table::load('responsive');

$sorts = [
	'name' => 'Name',
	'updated' => 'Active',
	'role' => 'Role'
];
$thead = [];
$query = $request_query;
$request_sort = $query['sort'] ?? '';
foreach($sorts as $sort=>$label) {
	if($request_sort==$sort) $label .= ' <span class="bi bi-sort-down"></span>';
	$query['sort'] = $sort; 
	$thead[] = anchor($request_page . '?' . http_build_query($query), $label);
}
$thead[] = '';
$table->setHeading($thead);

$tbody = [];
$btn_enable = '<button type="submit" name="enable" title="enable" value="%1$u" class="btn btn-success bi-check-circle"></button> ';
$btn_disable = '<button type="submit" title="disable" name="disable" value="%1$u" class="btn bi-x-circle btn-danger"></button>';

foreach($users as $user) {
	if($user->self()) $btns = '[self]' ;
	else {
		$username = htmlentities($user->name);
		$btns = $user->deleted_at ? 
			sprintf($btn_enable, $user->id, $username) : 
			sprintf($btn_disable, $user->id, $username) ;
		if($user->deleted_at) {
			$btns.= $delsure->button($user->id, "Delete {$username}");
		}
	}
	$updated = new \datetime($user->updated);
	$tbody[] = [
		sprintf('%s <a href="%s/view/%u">%s</a>',
			$user->deleted_at ? 
				'<span title="user disabled" class="bi-x-circle text-danger"></span>' : 
				'<span title="user enabled" class="bi-check-circle text-success"></span>',
			$base_url, 
			$user->id, 
			esc($user->name)
		),
		$updated->format('d M y'),
		$user->role,
		$btns
	];
}

echo form_open($current_url);
echo $table->generate($tbody);
echo form_close();

$this->endSection(); 

$this->section('bottom'); 
echo $delsure->form();
$this->endSection(); 
