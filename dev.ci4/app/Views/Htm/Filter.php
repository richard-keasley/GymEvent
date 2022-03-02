<?php namespace App\Views\Htm;

class Filter {
static $id = 0;
private $selector = '';

public function __construct($selector='table.table tr') {
	$this->selector = $selector;
}

public function htm() {
self::$id++;
$id = 'filter' . self::$id;

ob_start(); ?>
<div class="input-group mx-1" title="filter results">
<span class="input-group-text bi-filter"></span>
<input type="text" class="form-control" id="<?php echo $id;?>" placeholder="filter">
<script>
$(document).ready(function() {
$("#<?php echo $id;?>").on("keyup", function() {
	var value = $(this).val().toLowerCase();
	$("<?php echo $this->selector;?>").filter(function() {
		$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
	});
});
});
</script> 
</div>
<?php return ob_get_clean();
}

}
