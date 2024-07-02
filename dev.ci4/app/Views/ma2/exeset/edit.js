const exesets = {

filter: <?php 
	$arr = [];
	foreach(\App\Libraries\Mag\Exeset::filter as $key=>$val) {
		$arr[] = [$key, $val];	
	}
	echo json_encode($arr);
	?>,

exekeys: <?php echo json_encode(array_keys($exeset->exercises));?>,

fields: <?php echo json_encode($exeset_fields);?>,

exename: null,

data: null,

load: function(idx=0) {
	exesets.data = exesets.storage.load();
	if(!Array.isArray(exesets.data)) exesets.data = [];
	exesets.cleandata(idx);
},

update: function(idx=0) {
	var exeset = exesets.get_formdata(exesets.fields);
	console.log('get exeset from form');
	console.log(exesets.fields);
	console.log(exeset);
	exesets.data[idx] = exeset;
	exesets.cleandata(idx);
},

get_formdata: function(fields) {
	var formdata = {}, el = null;
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
},

cleandata: function(idx=0) {
	var exeset = exesets.data[idx] ?? {};
	console.log('clean exeset via API');
	
	var request = exeset;
	var api = '<?php echo site_url("/api/ma2/exeval");?>/';
	$.get(api, request, function(response) {
		//console.log(request);
		//console.log(response);
		try {
			// put clean data into store
			exesets.data[idx] = response['data'] ?? {};
			console.log(exesets.data[idx]);
			exesets.storage.save(exesets.data);

			var html = response['html'] ?? false;
			if(!html) throw new Error('No HTML returned');
			update_exevals(html, 1);
		}
		catch(errorThrown) { 
			update_exevals(errorThrown);
		}
	})
	.fail(function(jqXHR) {
		update_exevals('server error');
	});
},

storage: {
	load: function() {
		console.log('load exesets from local');
		return localStorage.getItem('mag-exesets');		
	},
	save: function(data) {
		console.log('store exesets to local');	
		console.log(data);
		localStorage.setItem('mag-exesets', JSON.stringify(data));
	}
} // end storage
	
};

const api = '<?php echo site_url("/api/mag/exevals");?>/';
const filter = <?php 
	$arr = [];
	foreach(\App\Libraries\Mag\Exeset::filter as $key=>$val) {
		$arr[] = [$key, $val];	
	}
	echo json_encode($arr);
?>;
const exekeys = <?php echo json_encode(array_keys($exeset->exercises));?>;
const exeset_fields = <?php echo json_encode($exeset_fields);?>;
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

$('#__editform button[name=update]').click(function() {
	get_exevals();
});

$('#editform [name=rulesetname]').change(function() {
	$('#editform').submit();
});

document.getElementById('execlear').addEventListener('show.bs.modal', function (event) {
	exename = $('#exes .nav-tabs .active').html();
	$('#execlear .exename').html(exename);
});

exesets.load();

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
	
	/*
	var form = document.getElementById("editform");
	var formData = new FormData(form);
	var postdata = {}
	for(var [key, value] of formData) {
		postdata[key] = value;
	}
	console.log(postdata);
	console.log(formData);
	*/
	
	
	
	var exeset = get_formdata(exeset_fields);
	console.log('get form data');
	console.log(exeset);
	return;
	
	var idx = 0; // this needs to be current gymnast
	exesets.data[idx] = exeset;
	exeset = exesets.cleandata(idx);
	return;
	
	// store this exeset
	var exesets = localStorage.getItem('mag-exesets');
	// console.log(exesets);



	// work out title from gymnast's name
	var name = exeset.name;
	console.log(name);
	exesets.filter.forEach((element) => {
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
	exeset_fields.forEach(fld => {
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
	exeset_fields.forEach(fldname => {
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
