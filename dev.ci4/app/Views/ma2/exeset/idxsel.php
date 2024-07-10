<div class="row d-print-none">
<div class="col-auto">
<select style="min-width:15em" class="form-select" name="idx" onchange="idxsel.reload();"></select>
</div>
<div class="col-auto">
<?php echo $idxsel; ?>
</div>
</div>
<script>
const idxsel = {
	selector: null,
	idx: 0,
	init: function() {
		idxsel.selector = $('select[name=idx]');
		idxsel.selector.html('');
		var data = exesets.storage.get();
		data.forEach(function(value, index, array) {
			var optionText = value['name'] ?? '??';
			idxsel.selector.append(new Option(optionText, index));
		});

		var idx = localStorage.getItem('mag-exesets-idx') ?? 0;
		// console.log(idx);
		idxsel.selector.val(idx);
	},
	reload: function(idx='#') {
		if(idx==='#') idx = parseInt(idxsel.selector.val());
		localStorage.setItem('mag-exesets-idx', idx);
		window.location.reload();
	}
}
$(function(){
idxsel.init();
})

</script>