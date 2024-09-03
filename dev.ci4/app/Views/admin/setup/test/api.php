<?php $this->extend('default');

$this->section('content');

$payload = [
	csrf_token() => csrf_hash(),
	'test' => "Random"
];
d($payload);

$attrs = [];
echo form_open('api/exeset/test/post', $attrs, $payload); ?>
<input type="submit" value="OK">
<button type="button" onclick="testapi()">API</button>
<?php echo form_close();

$this->endSection();

$this->section('top'); ?>

<?php $this->endSection();

$this->section('bottom'); 
d($_POST);
?>
<script>
function testapi() {
	var payload = <?php echo json_encode($payload);?>;
	var api = '<?php echo site_url("api/exeset/exeval");?>';
	// var api = '<?php echo site_url("/api/teamtime/control");?>';
	console.log(api, payload);
	$.post(api, payload)
		.done(function( response ) {
			console.log(  response );
		})
		.fail(function( response ) {
			console.log(  response );
		});
	
	
	
}
</script>
<?php 
$this->endSection();