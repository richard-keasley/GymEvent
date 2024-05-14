const api = '<?php echo site_url("/api/mag/exevals");?>/';
const filter = <?php 
	$arr = [];
	foreach(\App\Libraries\Mag\Exeset::filter as $key=>$val) {
		$arr[] = [$key, $val];	
	}
	echo json_encode($arr);
?>;
const exekeys = <?php echo json_encode(array_keys($exeset->exercises));?>;
const exeval_fields = <?php echo json_encode($exeval_fields);?>;
let execlearModal = null;
let exename = null;

function get_formdata(fields) {
	var formdata = {}; var el = null;
	$.each(fields, function(key, value) {
		if(typeof value=='object') {
			value = get_formdata(value);
		}
		else {
			el = $('[name='+value+']')[0];
			switch(el.type) {
				case 'checkbox':
					value = el.checked ? 1 : 0 ;
					break;
				default:
					value = el.value.trim();
			}
		}		
		formdata[key] = value;
	});
	return formdata;
}



<?php /*

if('serviceWorker' in navigator) {
	navigator.serviceWorker.register('/ma2/routineSW', {scope: '/ma2/'})
	.then((reg) => {
		// registration worked
		console.log('Registration succeeded. Scope is ' + reg.scope);
	}).catch((error) => {
		// registration failed
		console.log('Registration failed with ' + error);
	});
}
*/ ?>

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
	/*
	work out how to use local storage
	challenges:
	- allow multiple gymnasts per view
	- allow download of complete data set 
	*/
	
	
	var exeset = get_formdata(exeval_fields);
	// console.log(exeset);

	// store this exeset
	var exesets = localStorage.getItem('mag-exesets');
	// console.log(exesets);



	// work out title from gymnast's name
	var name = exeset.name;
	console.log(name);
	filter.forEach((element) => {
		var search = new RegExp(element[0], "gi");
		name = name.replace(search, element[1]);
	});
	if(name) { 
		$('h1').html(name);
		document.title = name;
	}
	
	
	
	
	
	
	return;
	// start here
	
	
	
		
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
	
	
	var exeset = {}; var el = null; var fldvalue = null;
	exeval_fields.forEach(fldname => {
		el = $('[name='+fldname+']')[0];
		switch(el.type) {
			case 'checkbox':
			fldvalue = el.checked ? 1 : 0 ;
			break;
			
			default:
			fldvalue = el.value;
		}
		
		var keys = fldname.split('_');
		var length = keys.length;
		// console.log(length, keys);
			
		// make space for arrays	
		if(length>1 && typeof exeset[keys[0]] === 'undefined') {
			exeset[keys[0]] = {};
		}
		if(length>2 && typeof exeset[keys[0]][keys[1]] === 'undefined') {
			exeset[keys[0]][keys[1]] = {};
		}
		if(length>3 && typeof exeset[keys[0]][keys[1]][keys[2]] === 'undefined') {
			exeset[keys[0]][keys[1]][keys[2]] = {};
		}
				
		switch(length) {
			case 2: 
			exeset[keys[0]][keys[1]] = fldvalue;
			break;
			case 3: 
			exeset[keys[0]][keys[1]][keys[2]] = fldvalue;
			break;
			case 4: 
			exeset[keys[0]][keys[1]][keys[2]][keys[3]] = fldvalue;
			break;
			default: // include length=1
			exeset[fldname] = fldvalue;
		}
	});
	// console.log(formData);
	console.log(exeset);
	
	var exesets = localStorage.getItem('mag-exesets');
	
	localStorage.setItem('mag-exeset', JSON.stringify(exeset));
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
