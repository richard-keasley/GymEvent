<?php namespace App\Views\Htm;

class Tabs {
public $id = '';
public $selected = null;
public $items = [];
public $format = [
	'element_start' => '<div id="%s">', 
	'headings_start' => '<nav class="nav nav-tabs" role="tablist">',
	'headings_end' => '</nav>',
	'panels_start' => '<div class="tab-content p-1 border border-top-0">',
	'panel_start' => '<div class="%s" id="%s" aria-labelledby="%s" role="tabpanel">',
	'panel_end' => '</div>',
	'panels_end' => '</div>',
	'element_end' => '</div>'
];

public function __construct($items=[], $id='tabs') {
	$this->items = $items;
	$this->id = $id;
}

public function set_item($heading, $content, $key=null) {
	$item = [
		'heading' => $heading,
		'content' => $content
	];
	if($key) $this->items[$key] = $item;
	else $this->items[] = $item;
}

public function htm() {

if(empty($this->items)) return;
if(is_null($this->selected)) $this->selected = array_key_first($this->items);
	
ob_start();

printf($this->format['element_start'], $this->id);

echo $this->format['headings_start'];
foreach($this->items as $key=>$item) {
	$active = $key===$this->selected;
	$attr = [
		'href' => "#{$this->id}item{$key}",
		'class' => $active ? 'nav-link active' : 'nav-link',
		'id' => "{$this->id}tab{$key}",
		'data-bs-toggle' => "tab",
		'role' => "tab",
		'aria-controls' => "{$this->id}item{$key}",
		'aria-selected' => $active ? 'true' : 'false'
	];
	printf('<a %s>%s</a>', stringify_attributes($attr), $item['heading']);
}
echo $this->format['headings_end'];
	
echo $this->format['panels_start'];
foreach($this->items as $key=>$item) {
	$active = $key===$this->selected;
	$class = $active ? 'tab-pane active' : 'tab-pane';
	$id = "{$this->id}item{$key}";
	$labelledby = "{$this->id}tab{$key}";
	printf($this->format['panel_start'], $class, $id, $labelledby);
	echo $item['content'];
	echo $this->format['panel_end'];
}
echo $this->format['panels_end'];

echo $this->format['element_end'];
?>
<script>
$(function() {
var elTabPanes = document.querySelector('#<?php echo $this->id;?>').querySelectorAll('.tab-pane');
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
<?php 
return ob_get_clean();
}
	
}
