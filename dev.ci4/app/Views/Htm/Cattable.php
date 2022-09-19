<?php namespace App\Views\Htm;

class Cattable {
public $data = null;
public $headings = []; // data columns to convert to HTM headings
public $table_header = false; // include table header
public $template_name = 'bordered'; // table template
private $compiled = null; // compiled data

public function __construct($headings=[]) {
	$this->headings = $headings;
}

public function compile() {
	if(!$this->data) {
		$this->compiled = false;
		return;
	}
	
	// headings
	$prev_headings = [];
	foreach($this->headings as $level=>$fldname) {
		$prev_headings[$level] = '';
	}
	$headings = [];

	// build categorised tables
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
	# d($compiled);
	$this->compiled = $compiled;
}
public function csv($data = false) {
	if($data) $this->data = $this->data;
	$this->compile();
	if(!$this->compiled) return;
	
	$blank_row = [''];
	
	ob_start(); 
	$fp =  fopen('php://output', 'w');
	foreach($this->compiled as $cattable) { 
		foreach($cattable['headings'] as $level=>$heading) {
			fputcsv($fp, [$heading]);
		}
		
		if($this->table_header) {
			$row = current($cattable['tbody']);
			$thead = array_keys($row);
			fputcsv($fp, $thead);
		}
		foreach($cattable['tbody'] as $row) {
			fputcsv($fp, $row);
		}
		fputcsv($fp, $blank_row);
	} 
	return ob_get_clean();
}

public function htm($data = false) {
	if($data) $this->data = $this->data;
	$this->compile();
	if(!$this->compiled) return;
		
	$table = \App\Views\Htm\Table::load($this->template_name);
	ob_start(); 
	?>
	<div class="cattables">
	<?php foreach($this->compiled as $cattable) { ?>
		<section><?php 
		
		foreach($cattable['headings'] as $level=>$heading) {
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
	<?php } ?>
	</div>
	<?php
	return ob_get_clean();
}

}
