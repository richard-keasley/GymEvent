<div class="row d-print-none my-1">
<div class="col-8 col-sm-9 col-md-10">
<select class="form-select" name="idx" onchange="exesets.idxsel.change();"></select>
</div>
<div class="col-4 col-sm-3 col-md-2"><?php 
echo $idxsel; 
?></div>
</div>
<script>
$(function(){
exesets.idxsel.init();
})
</script>