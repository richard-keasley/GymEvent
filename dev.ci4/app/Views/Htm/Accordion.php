<?php namespace App\Views\Htm;

class Accordion {
public $id = '';
public $items = [];

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
	ob_start();
	printf('<div class="accordion" id="%s">', $this->id); // accordion start
	foreach($this->items as $key=>$item) { ?>
		<div class="accordion-item">
		<?php
		// heading
		printf('<div class="accordion-header" id="%1$s-heading-%2$u">', $this->id, $key);
		printf('<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#%1$s-body-%2$u" aria-expanded="false" aria-controls="%1$s-body-%2$u">', $this->id, $key);
		echo $item['heading'];
		echo '</button></div>';
		// content
		printf('<div id="%1$s-body-%2$u" class="accordion-collapse collapse" aria-labelledby="%1$s-heading-%2$u" data-bs-parent="#%1$s">', $this->id, $key);
		printf('<div class="accordion-body table-responsive">%s</div>', $item['content']);
		echo '</div>'; 
		?>
		</div>
		<?php
	}
?>
<script>
$(function() {

let activeTab = localStorage.getItem('activeTab');
if(activeTab) $(activeTab).collapse('show');
$('#<?php echo $this->id;?> [data-bs-toggle=collapse]').on('click', function(e) {
	activeTab = e.target.getAttribute('data-bs-target');
	localStorage.setItem('activeTab', activeTab);
});	
});
</script>
<?php
	echo '</div>'; // accordion end
	return ob_get_clean();
}

}
