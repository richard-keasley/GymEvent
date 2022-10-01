<?php namespace App\Controllers\Setup;

class Db extends \App\Controllers\BaseController {
	
public function __construct() {
	$this->data['breadcrumbs'][] = 'admin';
	$this->data['breadcrumbs'][] = 'setup';
	$this->data['breadcrumbs'][] = ['setup/dev', 'development'];
	$this->data['breadcrumbs'][] = ['setup/db', 'database'];
}
	
public function index() {
	$this->data['title'] = 'Database structure';
	$this->data['heading'] = $this->data['title'];
	return view('admin/setup/db', $this->data);
}

public function orphans() {
	$this->_orphans();
	
	if($this->request->getPost('cmd')=='commit') {
		$db = \Config\Database::connect();
		$msgs = [] ;
		foreach($this->data['kills'] as $sql) {
			# $sql = '-- ' . $sql;
			$done = $db->query($sql) ? 'done' : 'fail';
			$msgs[] = "{$sql} ({$done})";		
		}
		$this->data['messages'][] = [implode('<br>', $msgs), 'info'];
		// requery
		$this->_orphans();
	}
	
	// view
	if(!$this->data['kills']) {
		$this->data['messages'][] = ['No orphans found', 'success'];
	}
	$this->data['title'] = 'Database orphans';
	$this->data['heading'] = $this->data['title'];
	$this->data['breadcrumbs'][] = 'setup/db/orphans';
	return view('admin/setup/orphans', $this->data);
}

private function _orphans() {
	$this->data['kills'] = [];
	$this->data['tables'] = []; 
	
	$sql_select = "SELECT 
	`child_table`.`id` AS 'child',
	`child_table`.`child_field` AS 'parent'
	FROM `child_table` 
	LEFT JOIN `parent_table` 
	ON `child_table`.`child_field`=`parent_table`.`id`
	WHERE `parent_table`.`id` IS NULL;";

	$sql_delete = "DELETE FROM `child_table` WHERE `id`=child_id;";

	$trans = [
	[
		'parent_table' => 'events',
		'child_table' => 'evt_disciplines',
		'child_field' => 'event_id'
	],
	[
		'parent_table' => 'evt_disciplines',
		'child_table' => 'evt_categories',
		'child_field' => 'discipline_id'
	],
	[
		'parent_table' => 'evt_categories',
		'child_table' => 'evt_entries',
		'child_field' => 'category_id'
	],
	[
		'parent_table' => 'users',
		'child_table' => 'evt_entries',
		'child_field' => 'user_id'
	],
	[
		'parent_table' => 'users',
		'child_table' => 'clubrets',
		'child_field' => 'user_id'
	],
	[
		'parent_table' => 'events',
		'child_table' => 'clubrets',
		'child_field' => 'event_id'
	]
	];
	
	$db = \Config\Database::connect();
	foreach($trans as $tran) {	
		$datarow = $tran;
		$sql = strtr($sql_select, $tran);
		$datarow['tbody'] = $db->query($sql)->getResultArray();
		$this->data['tables'][] = $datarow;
		foreach($datarow['tbody'] as $row) {
			$tran['child_id'] = $row['child'];
			$this->data['kills'][] = strtr($sql_delete, $tran);
		}
	}	
}

}
