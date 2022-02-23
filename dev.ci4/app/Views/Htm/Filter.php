<?php namespace App\Views\Htm;

class Filter {
public $selector = '';

public function __construct($selector='table.table tr') {
	$this->selector = $selector;
}

public function htm() {
ob_start(); ?>
<div class="my-1">
<span class="bi-filter m-1"></span>
<input title="filter results" id="livefilter" class="form-input">
<script>
$(document).ready(function() {
$("#livefilter").on("keyup", function() {
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
