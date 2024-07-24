<div class="row d-print-none">
<div class="col-auto">
<select style="min-width:15em" class="form-select" name="idx" onchange="idxsel.change();"
onfocusin="idxsel.update();"></select>
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
	idxsel.selector.val(idx);
},

change: function(idx='#') {
	if(idx==='#') idx = parseInt(idxsel.selector.val());
	var stored = parseInt(localStorage.getItem('mag-exesets-idx'));
	if(stored===idx) return;
	localStorage.setItem('mag-exesets-idx', idx);
	window.location.reload();
},

update: function() {
	if(location.pathname!='/ma2/routine') return;
	exesets.update();
}

}

$(function(){
idxsel.init();
})

</script>