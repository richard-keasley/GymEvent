<?php namespace App\Libraries\Rulesets;

class Exeset {
const filter = [
	'<' => '{',
	'>' => '}',
	'&' => '+'
];

static function sanitize($arr) {
	foreach($arr as $key=>$val) {
		$arr[$key] = is_array($val) ? 
			self::sanitize($val) : 
			strtr(trim($val), self::filter) ;
	}
	return $arr;
}

static function read_json($json) {
	// returns an array of routine sets
	$retval = [
		'error' => null,
		'exesets' => []
	];
	try {
		# d($json);
		$flags = JSON_THROW_ON_ERROR;
		$arr = json_decode($json, true, 512, $flags);
		# d($arr);
		foreach($arr as $request) {
			$retval['exesets'][] = new self($request);
		}			
	}
	catch(\JsonException $ex) {
		$retval['error'] = "{$ex->getMessage()}. Check the file is valid JSON.";
	}
	catch(\Exception $ex) {
		$retval['error'] = $ex->getMessage();
	}
	return $retval;
}

public $data = [];
public $exercises = [];
public $ruleset = null;

public function __construct($request=[]) {
	// sanitize
	$request = self::sanitize($request);
	
	$val = $request['rulesetname'] ?? '#';
	$this->ruleset = \App\Libraries\Rulesets::load($val);
	
	$this->data = [
		'name' => $request['name'] ?? '',
		'event' => $request['event'] ?? '',
		'club' => $request['club'] ?? '',
		'rulesetname' => $this->ruleset->name,
		'saved' => date('Y-m-d H:i:s')
	];
	if(!$this->data['name']) $this->data['name'] = '[no name]';
			
	foreach($this->ruleset->exes as $exekey=>$exe_rules) {
		$exe_rules = $this->ruleset->$exekey;
		
		switch($exe_rules['method']) {
			case 'tariff':
			$el_count = $exe_rules['exe_count'];
			$col_count = 3; // tariff, group, description
			break;
				
			case 'routine':
			default:
			$el_count = count($this->ruleset->routine['short']) - 1;
			$col_count = 3; // value, group, description
		}
		
		$element = array_fill(0, $col_count,'');
		$elements = array_fill(0, $el_count, $element);
		foreach($elements as $elnum=>$element) {
			foreach($element as $colnum=>$default) {
				$elements[$elnum][$colnum] = $request[$exekey]['elements'][$elnum][$colnum] ?? $default ;
			}
			switch($exe_rules['method']) {
				case 'tariff':
				$tariff = (float) $elements[$elnum][0];
				$tariff = abs($tariff) ;
				while($tariff > 10) { $tariff = $tariff / 10; }
				$elements[$elnum][0] = $tariff ? number_format($tariff, 1) : '';
				break;
			}
		}
		$this->exercises[$exekey]['elements'] = $elements;
		
		if($exe_rules['connection']) {
			$val = (float) ($request[$exekey]['connection'] ?? 0);
			while($val > 4) { $val = $val / 10; }
			$this->exercises[$exekey]['connection'] = number_format($val, 1);	
		}
		
		$neutrals = [];
		foreach(array_keys($exe_rules['neutrals']) as $nkey) {
			$val = $request[$exekey]['neutrals'][$nkey] ?? 0 ;
			$neutrals[$nkey] = $val ? 1 : 0;
		}
		$this->exercises[$exekey]['neutrals'] = $neutrals;	
	}
}

public function __get($propname) {
	if(isset($this->data[$propname])) return $this->data[$propname];
}

public function export() {
	$retval = $this->data;
	
	$retval['ruleset'] = [
		'name' => $this->ruleset->name,
		'title' => $this->ruleset->title,
		'description' => $this->ruleset->description,
		'version' => $this->ruleset->version,
	];
			
	foreach($this->exercises as $exekey=>$exercise) {
		$retval[$exekey] = $exercise;
	}
	return $retval;
}

}
