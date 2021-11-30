<script>
function get_error(jqXHR) { 
	var msg = '';
	if(typeof jqXHR.responseJSON==='undefined') {
		msg = jqXHR.responseText;
	}
	else {
		msg = jqXHR.responseJSON;
		if(typeof msg.messages!=='undefined') msg = msg.messages;
		if(typeof msg.error!=='undefined') msg = msg.error;
		if(typeof msg.message!=='undefined') msg = msg.message;
	}
	if(!msg) msg = 'Undefined error';
	return jqXHR.status + ': ' + msg;
}
</script>
