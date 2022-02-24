<?php namespace App\Views\Htm;

class Filter {

static function htm($selector='table.table tr') {
ob_start(); ?>
<div class="input-group mx-1" title="filter results">
<span class="input-group-text bi-filter"></span>
<input type="text" class="form-control" id="livefilter" placeholder="filter">
<script>
$(document).ready(function() {
$("#livefilter").on("keyup", function() {
	var value = $(this).val().toLowerCase();
	$("<?php echo $selector;?>").filter(function() {
		$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
	});
});
});
</script> 
</div>
<?php return ob_get_clean();
}

}
