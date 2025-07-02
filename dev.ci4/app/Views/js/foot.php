<script>
function get_error(jqXHR) { 	var status = jqXHR.status ?? 0;	if(!status) return ''; // no status on reload
	try {		var messages = jqXHR.responseJSON.messages ?? {} ;		var message = messages.error ??			messages.message ?? 			null ;		} 
	catch(err) {		message = err.message;	}
	if(!message) message = 'Undefined error';	return status + ': ' + message;	}
<?php if($serviceworker) { ?>
if('serviceWorker' in navigator) {	// console.log("register service worker");	navigator.serviceWorker.register('<?php echo base_url('sw.js');?>')	.then(function(reg) {		// console.log('Registration succeeded');	})	.catch(function(error) {		// console.warn('Registration failed - ' + error);	});} else {	// console.log("serviceWorker not available");}<?php } ?></script>