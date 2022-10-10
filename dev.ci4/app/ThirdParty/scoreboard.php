<?php namespace App\ThirdParty;

class scoreboard {
public $error = null;
public $tables = [];
private $db = null;

function __construct() {
	$files = new \CodeIgniter\Files\FileCollection([__DIR__ . '/scoreboard']);
	foreach($files as $file) {
		$varname = $file->getBasename('.php');
		$this->tables[$varname] = $file;
	}
}

public function query($sql) {
	if(!$this->db) return null;
	try {
		$res = $this->db->simpleQuery($sql);
		return $res->fetch_all(MYSQLI_ASSOC);
	}
	catch(\Exception $ex) {
		echo $ex->getMessage();
	}
	return [];
}

function init_db() {
	$config = config('Database');
	$database = new \CodeIgniter\Database\Database;
	try {
		$this->db = $database->load($config->scoreboard, 'scoreboard');
		$this->db->initialize();
	}
	catch(\CodeIgniter\Database\Exceptions\DatabaseException $ex) {
		echo $ex->getMessage();
		$this->db = null;
	}
	catch(\ErrorException $ex) {
		echo $ex->getMessage();
		$this->db = null;
	}
	return $this->db ? true : false;
}

function get_time($varname, $format='Y-m-d H:i:s') {
	$include = $this->get_include($varname);
	return $include ?
		date($format, $include->getMTime()) : 
		null;
}

function get_table($varname) {
	$include = $this->get_include($varname);
	if($include) {
		include $include->getPathname();
		return $$varname;
	}
	return [];
}

function get_include($varname) {
	if(isset($this->tables[$varname])) return $this->tables[$varname];
	$this->error = "Can't find scoreboard data file {$varname}";
	return false;
}

function get_exesets() {
	$tables = [];
	foreach(['exerciseset', 'exercises'] as $key=>$varname) {
		$tables[$key] = $this->get_table($varname);
		if(!$tables[$key]) return false;
	}
	return $this->join_tables($tables[0], $tables[1], 'SetId');
}

function get_disciplines() {
	$tables = [];
	foreach(['disciplinecategory', 'disciplines'] as $key=>$varname) {
		$tables[$key] = $this->get_table($varname);
		if(!$tables[$key]) return false;
	}
	return $this->join_tables($tables[0], $tables[1], 'CategoryId');
}

function get_discats() {
	$retval = [];
	$cats = $this->get_table('disciplinecategory');
	$diss = $this->get_table('disciplines');
	if(!$cats || !$diss) return $retval;
	foreach($cats as $key=>$cat) {
		$cat['disciplines'] = [];
		foreach($diss as $dis) {
			if($dis['CategoryId']==$cat['CategoryId']) $cat['disciplines'][] = $dis;
		}
		$retval[] = $cat;
	}
	return $retval;
}

private function join_tables($parents, $children, $parent_key, $join_key='') {
	if(!$join_key) $join_key = $parent_key;
	$retval = []; 
	foreach($parents as $parent) {
		$parent['children'] = [];
		foreach($children as $child) {
			if($child[$join_key]==$parent[$parent_key]) $parent['children'][] = $child;
		}
		$retval[] = $parent;
	}
	return $retval;
}

}
