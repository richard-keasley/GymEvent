<?php $this->extend('default');

$this->section('content');

$request = [
	'test' => "Random",
	'value' => '999',
];

$attrs = [
	'id' => "apiform",
];
echo form_open('', $attrs, $request); ?>
<button type="submit">OK</button>
<button type="button" onclick="testapi('post')">post API</button>
<button type="button" onclick="testapi('get')">get API</button>
<?php echo form_close();

$this->endSection();

$this->section('top'); ?>

<?php $this->endSection();

$this->section('bottom'); ?>

<h6>Form vars</h6>
<pre><?php 
	foreach($request as $key=>$val) 
	echo "{$key}: {$val}\n";
?></pre>

<h6>HTM request</h6>
<pre id="htmrequest"></pre>

<h6>HTM response</h6>
<pre><?php 
	foreach($_POST as $key=>$val) 
	echo "{$key}: {$val}\n";
?></pre>

<h6>API request</h6>
<pre id="apirequest"></pre>

<h6>API response</h6>
<pre id="apiresponse"></pre>

<script>
$(function() {
	var arr = {};
	$.each($('#apiform').serializeArray(), function(i, field) {
		arr[field.name] = field.value;
	});
	htm_arr(arr, '#htmrequest'); 
});

function htm_arr(arr, selector) {
	var htm = '';
	$.each(arr, function(key, value) {
		htm += `${key}: ${value} \n`;
	});
	$(selector).html(htm);
}

function testapi(method='post') {
	var request = <?php echo json_encode($request);?>;
	var api = '<?php 
		$api = 'api/home/test';
		if($param) $api .= "/{$param}";
		echo site_url($api); 
		?>';
	
	switch(method) {
		case 'get':
		$.get(api, request)
		.done(function(response) {
			api_response(response);	
		})
		.fail(function(response) {
			api_response(response, true);
		});
		break;
		
		default:
		method = 'post';
		request = securepost(request);
		$.post(api, request)
		.done(function(response) {
			api_response(response);	
		})
		.fail(function(response) {
			api_response(response, true);
		});
	}
	
	// console.log(api, request);
	htm_arr(request, '#apirequest');
}

function api_response(response, is_err=false) {
	var htm = '';
	
	if(is_err) {
		// console.error(response);
		htm = get_error(response);
		$('#apiresponse').html();
	}
	else {
		// console.log(response);
		htm = JSON.stringify(response, null, 2);
	}
	$('#apiresponse').html(htm);
}
</script>
<?php 
$this->endSection();