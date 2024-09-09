<?php namespace App\Views\Htm;

/*
$cattable = new \App\Views\Htm\Cattable($headings);
$cattable->data = $tbody;
echo $cattable->htm();
*/

class Cattable implements \stringable {
public $data = null;
public $headings = []; // data columns to convert to HTM headings
public $table_header = false; // include table header
public $template_name = 'bordered'; // table template
private $_compiled = []; // compiled data

public function __construct($headings=[]) {
	$this->headings = $headings;
	$this->_compiled = [];
}

public function __get($key) {
	return match($key) {
		'compiled' => $this->compile(),
		default => null
	};
}

private function compile() {
	if($this->_compiled) return $this->_compiled; // already done
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
	$this->_compiled = $compiled;
	return $this->_compiled;
}

public function csv() {
	$csv = new \App\Libraries\Csv;
	foreach($this->compiled as $cattable) { 
		foreach($cattable['headings'] as $level=>$heading) {
			$csv->add_row([$heading]);
		}
		$csv->add_row(['']);
		
		$csv->add_table($cattable['tbody'], true);
		$csv->add_row(['']);
	} 
	ob_start(); 
	$csv->write('php://output');
	return ob_get_clean();
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
