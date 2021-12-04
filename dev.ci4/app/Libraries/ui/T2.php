<?php namespace App\Libraries\Ui;

class T2 {
public $id = '';
public $selected = 0;
public $data = [];
public $format = [
	'headings_start' => '<nav class="nav nav-tabs" role="tablist">',
	'headings_end' => '</nav>',
	'items_start' => '<div id="%s" class="tab-content">',
	'item_start' => '<div class="%s" id="%s" aria-labelledby="%s" role="tabpanel">',
	'item_end' => '</div>',
	'items_end' => '</div>'
];

public function __construct($data=[], $id='tabs') {
	$this->data = $data;
	$this->id = $id;
	$this->selected = array_key_first($data);
}

public function htm() {
ob_start();

echo $this->format['headings_start'];
foreach($this->data as $key=>$row) {
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
	echo '<a';
	foreach($attr as $key=>$val) {
		printf(' %s="%s"', $key, $val);
	}
	echo '>' . $row['heading'] . '</a>';
}
echo $this->format['headings_end'];
	
printf($this->format['items_start'], $this->id);
foreach($this->data as $key=>$row) {
	$active = $key===$this->selected;
	$class = $active ? 'tab-pane active' : 'tab-pane';
	$id = "{$this->id}item{$key}";
	$labelledby = "{$this->id}tab{$key}";
	printf($this->format['item_start'], $class, $id, $labelledby);
	echo $row['content'];
	echo $this->format['item_end'];
}

echo $this->format['items_end'];
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
