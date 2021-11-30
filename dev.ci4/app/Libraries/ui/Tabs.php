<?php namespace App\Libraries\Ui;

class Tabs {
private $id = '';
private $in_panel = false;
private $in_content = false;
public $selected = 0;
public $tabs = [];
public $format = [
	'tabs_start' => '<nav class="nav nav-tabs" role="tablist">',
	'tabs_end' => '</nav>',
	'content_start' => '<div id="%s" class="tab-content">',
	'panel_start' => '<div class="%s" id="%s" aria-labelledby="%s" role="tabpanel">',
	'panel_end' => '</div>',
	'content_end' => '</div>'
];

public function __construct($tabs, $id='tabs') {
	$this->tabs = $tabs;
	$this->id = $id;
	$this->selected = array_key_first($tabs);
}

public function tabs() {
	$buffer = '';
	foreach($this->tabs as $key=>$label) {
		$active = $key===$this->selected;
		$attr = [
			'href' => "#{$this->id}panel{$key}",
			'class' => $active ? 'nav-link active' : 'nav-link',
			'id' => "{$this->id}tab{$key}",
			'data-bs-toggle' => "tab",
			'role' => "tab",
			'aria-controls' => "{$this->id}panel{$key}",
			'aria-selected' => $active ? 'true' : 'false'
		];
		$buffer .= '<a';
		foreach($attr as $key=>$val) $buffer .= sprintf(' %s="%s"', $key, $val);
		$buffer .= '>' . $label . '</a>';
	}
	return $this->format['tabs_start'] . $buffer . $this->format['tabs_end'];
}

function content_start() {
	$this->in_content = true;
	return sprintf($this->format['content_start'], $this->id);
}

function panel_start($key) {
	$buffer = ''; 
	if(!$this->in_content) $buffer .= $this->content_start();
	if($this->in_panel) $buffer .= $this->panel_end();
	$this->in_panel = true;
	
	$active = $key===$this->selected;
	$class = $active ? 'tab-pane active' : 'tab-pane';
	$id = "{$this->id}panel{$key}";
	$labelledby = "{$this->id}tab{$key}";
	$buffer .= sprintf($this->format['panel_start'], $class, $id, $labelledby);
	return $buffer;
}

function panel_end() {
	$this->in_panel = false;
	return $this->format['panel_end'];
}

function content_end() {
	$buffer = ''; 
	if($this->in_panel) $buffer .= $this->panel_end();
	if($this->in_content) $buffer .= $this->format['content_end'];
	$this->in_content = false;
	
	$selector = $this->id;
	$buffer .= <<<EOT
<script>
$(function() {
var elTabPanes = document.querySelector('#$selector').querySelectorAll('.tab-pane');
var activeTab = localStorage.getItem('activeTab');
var i, tabNames = [];
for (i = 0; i < elTabPanes.length; ++i) {
	tabNames[i] = '#' + elTabPanes[i].id;
}
if(!activeTab || !tabNames.includes(activeTab)) activeTab = tabNames[0];
$('[href="' + activeTab + '"]').tab('show');
$('[data-bs-toggle=tab]').on('show.bs.tab', function(e) {
	var activeTab = e.target.hash;
	localStorage.setItem('activeTab', e.target.hash);
});
});
</script>
EOT;
	return $buffer;
}
	
}