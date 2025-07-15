<?php namespace App\Views\Htm;

/*
$cattable = new \App\Views\Htm\Cattable($tbody, $headings);
echo $cattable;
*/

class Cattable implements \stringable {
public $table_header = false; // include table header
public $template_name = 'bordered'; // table template

private $attribs = [];

public function __construct($data, $headings=[]) {
	$this->attribs = [
		'data' => $data, // table body
		'headings' => $headings // columns to be used as headings
	];
}

public function __get($key) {
	if(isset($this->attribs[$key])) return $this->attribs[$key];
	
	$val = match($key) {
		'compiled' => $this->compile(), // compiled data
		'flattened' => $this->flatten(), // ready for csv
		'tree' => $this->tree(), // ready for export
		default => null
	};
	$this->attribs[$key] = $val;
	return $val;
}

private function tree() {
	$retval = [];
	foreach($this->compiled as $cattable) {
		$retval[] = [
			'level' => array_key_first($cattable['headings']),
			'heading' => implode(', ', $cattable['headings']),
			'tbody' => $cattable['tbody'],
		];
	}
	return $retval;
}

private function compile() {
	if(!$this->data) return []; // no data
	if(!$this->headings) return []; // no headings set
	
	// headings
	$prev_headings = [];
	foreach($this->headings as $level=>$fldname) {
		$prev_headings[$level] = '';
	}

	// build categorised tables
	$headings = [];
	$compiled = [];
	$catkey = 0;
	foreach($this->data as $row) {
		// headings
		foreach($this->headings as $level=>$fldname) {
			if(isset($row[$fldname])) {
				$heading = $row[$fldname];
				// check if new category
				if($headings || ($heading!=$prev_headings[$level])) {
					$prev_headings[$level] = $heading;
					$headings[$level] = $heading;
				}
				// remove headings from row
				unset($row[$fldname]);
			}
		}
		
		if($headings) {
			$catkey++;
			$compiled[$catkey] = [
				'headings' => $headings,
				'tbody' => []
			];
			#$prev_headings = $headings;
			$headings = [];
		}
		$compiled[$catkey]['tbody'][] = $row;
	}
	# dd($compiled);
	return $compiled;
}

private function flatten() {
	$retval = [];
	foreach($this->compiled as $cattable) { 
		foreach($cattable['headings'] as $level=>$heading) {
			$retval[] = [$heading];
		}
		$retval[] = [''];
		$thead = $this->table_header; // need header?
		foreach($cattable['tbody'] as $row) {
			if($thead) $retval[] = array_keys($row);
			$thead = false;
			$retval[] = $row;
		}
		$retval[] = [''];
	}
	return $retval;
}

public function __toString() {
	# d($this->compiled);
	ob_start(); 
	$table = \App\Views\Htm\Table::load($this->template_name);
	foreach($this->compiled as $cattable) { ?>
		<section class="mw-100"><?php 
		
		$headings = $cattable['headings'] ?? [] ;
		foreach($headings as $level=>$heading) {
			$hl = $level + 2; // heading level
			$tag = $hl>6 ? 'p' : "h{$hl}"; 
			echo "<{$tag}>{$heading}</{$tag}>";
		}
		
		if($this->table_header) {
			$row = current($cattable['tbody']);
			$thead = array_keys($row);
			$table->setHeading($thead);		
		}
		else {
			$table->autoHeading = false;
		}
		echo $table->generate($cattable['tbody']);
		
		?></section>
	<?php }
	return ob_get_clean();
}

}
