<?php
ob_start(); 
echo script_tag('app/bootstrap.bundle.min.js');

?>
<script>

function get_error(jqXHR) {
	var status = jqXHR.status ?? 0;
	if(!status) return ''; // no status on reload
	
	var response = jqXHR.responseJSONx ?? {};
	var messages = response.messages ?? {} ;
	var message = response.message ?? 
		messages.error ??
		messages.message ?? 
		error_reason(status) ;
	return status + ': ' + message;	
}

function error_reason(status) {
	var reasons = {
		401: 'Unauthorised',
		403: 'Forbidden',
		404: 'Not Found',
		423: 'Locked',
		429: 'Too Many Requests'
	};
	reason = reasons[status.toString()] ?? null;
	if(reason) return reason;
	return status>499 ? 'server error' : 'client error' ;
}

function show_busy(el) {
	$btns = $(el).find('button.btn');
	$btns.attr('disabled', 'disabled')
		.html('<span class="spinner-border spinner-border-sm" role="status"></span> wait');
}

function securepost(request) {
<?php
// add in security fields before posting

// what filters are active for this request
$found = [];
$searches = ['csrf', 'honeypot'];
foreach(service('filters')->getFilters() as $arr) {
	foreach($searches as $search) {
		if(in_array($search, $arr)) $found[$search] = $search;
		if(isset($arr[$search])) $found[$search] = $search;
	}
}

// add the relevant key / value to request
foreach($found as $filtername) {
	switch($filtername) {
		case 'csrf':
		$key = csrf_token();
		$val = csrf_hash();
		break;

		case 'honeypot':
		$key = config('Honeypot')->name;
		$val = '';
		break;
	}
	echo "request['{$key}'] = '{$val}';\n";
}
?>
return request;
}

<?php if($serviceworker ?? false) { ?>
if('serviceWorker' in navigator) {
	// console.log("register service worker");
	navigator.serviceWorker.register('<?php echo base_url('sw.js');?>')
	.then(function(reg) {
		// console.log('Registration succeeded');
	})
	.catch(function(error) {
		// console.warn('Registration failed - ' + error);
	});
} 
else {
	// console.log("serviceWorker not available");
}
<?php } ?>
</script>
<?php

$minifier = new MatthiasMullie\Minify\JS();
$minifier->add(ob_get_clean());
echo $minifier->minify();
