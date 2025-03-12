<div class="row d-print-none">
<div class="col-auto">
<select style="min-width:10em" class="form-select" name="idx" onchange="exesets.idxsel.change();"></select>
</div>
<div class="col-auto"><?php 
echo $idxsel; 
?></div>
</div>
<script>
$(function(){
exesets.idxsel.init();
})

</script>