
/*

if('serviceWorker' in navigator) {
	navigator.serviceWorker.register('/mag/routineSW', {scope: '/mag/'})
	.then((reg) => {
		// registration worked
		console.log('Registration succeeded. Scope is ' + reg.scope);
	}).catch((error) => {
		// registration failed
		console.log('Registration failed with ' + error);
	});
}
*/

$(function() {

$('#editform button[name=clone]').click(function() {
	var form = $('#editform')[0];
	var name_field = $('#editform [name=name]');
	var name = name_field.val();
	form.target = '_blank';
	name_field.val('copied');
	$('#editform').submit();
	form.target = '_self';
	name_field.val(name);
});

$('#editform button[name=update]').click(function() {
	get_exevals();
});

$('#editform [name=rulesetname]').change(function() {
	$('#editform').submit();
});

execlearModal = document.getElementById('execlear');
execlearModal.addEventListener('show.bs.modal', function (event) {
	exename = $('#exes .nav-tabs .active').html();
	$('#execlear .exename').html(exename);
});

});

function execlear(exekey) {
	$('#exes .tab-pane.active select').val('');
	$('#exes .tab-pane.active input[type=text]').val('');
	$('#exes .tab-pane.active input[type=number]').val(0);
	$('#exes .tab-pane.active input[type=checkbox]').prop("checked", false);
	get_exevals();
}

function get_exevals() {
	// work out title from gymnast's name
	var name = $('[name=name]').val().trim();
	filter.forEach((element) => {
		var search = new RegExp(element[0], "gi");
		name = name.replace(search, element[1]);
	});
	if(name) { 
		$('h1').html(name);
		document.title = name;
	}
		
	var exeset = {}; var el = null; var val = null;
	exeval_fields.forEach(fld => {
		el = $('[name='+fld+']')[0];
		switch(el.type) {
			case 'checkbox':
				val = el.checked ? 1 : 0 ;
				break;
			default:
				val = el.value;
		}
		exeset[fld] = val;
	});

	$.get(api, exeset, function(response) {
		try { 
			update_exevals(response, 1); 
		}
		catch(errorThrown) { 
			update_exevals(errorThrown);
		}
	})
	.fail(function(jqXHR) {
		update_exevals('server error');
		update_exevals(get_error(jqXHR));
	});
}

function update_exevals(message, message_ok=0) {
	let htm = ''; let this_ok = 0;
	exekeys.forEach(function(exekey) {
		this_ok = message_ok ? typeof(message[exekey])!="undefined" : 0 ;
		if(this_ok) {
			htm = message[exekey];
		}
		else {
			htm = message_ok ? exekey + ' missing in response' : message ;
		}
		if(!this_ok) htm = '<div class="p-1 alert alert-danger"><ul class="list-unstyled m-0"><li>' + htm + '</li></ul></div>';
		$('#exeval-'+exekey).html(htm);
	});
}
