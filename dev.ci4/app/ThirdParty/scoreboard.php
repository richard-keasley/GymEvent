<?php namespace App\ThirdParty;

class scoreboard {
public $error = null;

function get_time($varname, $format='Y-m-d H:i:s') {
	$include = __DIR__ . "/scoreboard/{$varname}.php";
	if(file_exists($include)) {
		return date($format, filemtime($include));
	}
	else {
		$this->error = "Can't find scoreboard data file {$varname}";
		return null;
 	}
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

function get_table($varname) {
	$include = __DIR__ . "/scoreboard/{$varname}.php";
	if(file_exists($include)) {
		include $include;
	}
	else {
		$this->error = "Can't find scoreboard data file {$varname}";
		return [];
 	}
	return $$varname;
}

function join_tables($parents, $children, $parent_key, $join_key='') {
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
