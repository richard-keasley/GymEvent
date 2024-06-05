<?php namespace App\ThirdParty;

class scoreboard {
public $error = null;
public $tables = [];
public $files = null;
private $db = null;

function __construct() {
	$this->files = new \CodeIgniter\Files\FileCollection([__DIR__ . '/scoreboard']);
	foreach($this->files as $file) include $file->getPathname();
}

public function query($sql) {
	try {
		if(!$this->db) {
			$this->db = \Config\Database::connect('scoreboard');
		}
		$res = $this->db->simpleQuery($sql);
		return $res->fetch_all(MYSQLI_ASSOC);
	}
	catch(\Exception | \CodeIgniter\Database\Exceptions\DatabaseException $ex) {
		$this->error = $ex->getMessage();
	}
	return null;
}

function get_time($varname, $format='yyyy-MM-dd HH:mm:ss') {
	$time = $this->tables[$varname]['time'] ?? null ;
	if(!$time) return null;
	$time = new \CodeIgniter\I18n\Time($time);
	return $time->toLocalizedString($format);
}

private function get_table($varname) {
	return $this->tables[$varname]['table'] ?? [] ;
}

function get_file($varname) {
	foreach($this->files as $file) {
		$basename = $file->getBaseName('.php');
		if($basename==$varname) return $file;
	}
	$this->error = "Can't find scoreboard data file {$varname}";
	return null;
}

function get_exesets() {
	$tables = [];
	foreach(['exerciseset', 'exercises'] as $varname) {
		$tables[$varname] = $this->get_table($varname);
		if(!$tables[$varname]) return [];
	}
	
	// clean exercise sets
	$appvars = new \App\Models\Appvars();
	$include = $appvars->get_value("scoreboard.exesets");
	if($include) {
		$temp = [];
		foreach($tables['exerciseset'] as $row) {
			$key = array_search($row['SetId'], $include);
			if($row['SetId']==11) {
				// rename "F&V no music";
				$row['Name'] = 'Floor &amp; Vault';
			}
			if($key!==false) $temp[$key] = $row;
		}
		ksort($temp);
		$tables['exerciseset'] = $temp;
	}
		
	$retval = [];
	foreach($tables['exerciseset'] as $exeset) {
		$exeset['children'] = [];
		$sort = [];
		foreach($tables['exercises'] as $exercise) {
			if($exercise['SetId']==$exeset['SetId']) {
				$sort[] = $exercise['Order'];
				$exeset['children'][] = $exercise;
			}
		}
		if($exeset['children']) {
			array_multisort($sort, $exeset['children']);
			$retval[] = $exeset;
		}
	}
	return $retval;
}

function get_discats() {
	$retval = [];
	$cats = $this->get_table('disciplinecategory');
	$disciplines = $this->get_table('disciplines');
	
	// clean disciplines
	$appvars = new \App\Models\Appvars();
	$include = $appvars->get_value("scoreboard.disciplines");
	if($include) {
		$temp = [];
		foreach($disciplines as $row) {
			$key = array_search($row['DisId'], $include);
			if($key!==false) $temp[$key] = $row;
		}
		ksort($temp);
		$disciplines = $temp;
	}
		
	if(!$cats || !$disciplines) return $retval;
	foreach($cats as $key=>$cat) {
		$cat['disciplines'] = [];
		foreach($disciplines as $dis) {
			if($dis['CategoryId']==$cat['CategoryId']) $cat['disciplines'][] = $dis;
		}
		if($cat['disciplines']) $retval[] = $cat;
	}
	return $retval;
}

}
