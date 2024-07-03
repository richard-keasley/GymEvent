const exesets = {
	
idx: <?php echo $idx;?>,

exekeys: <?php echo json_encode(array_keys($exeset->exercises));?>,

load: function() {
	var exeset = exesets.storage.load();
	exesets.cleandata(exeset);
},

update: function() {
	var exeset = exesets.formdata.get();
	exesets.cleandata(exeset);
},

clone: function() {
	var exeset = exesets.formdata.get();
	exeset['name'] = '# new';
	exesets.storage.add(exeset);
	exesets.update();
},

delete: function() {
	exesets.storage.delete();
	idxsel.reload(0);
},

formdata: {
	fields: <?php echo json_encode($exeset_fields);?>,

	get: function(fields=null) {
		if(!fields) {
			fields = exesets.formdata.fields;
			console.log('get form data');
			// console.log(fields);
		}
		
		var formdata = {}, el = null;
		$.each(fields, function(key, value) {
			if(typeof value=='object') {
				value = exesets.formdata.get(value);
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
	
	set: function(data, fields=null) {
		if(!fields) {
			fields = exesets.formdata.fields;
			exesets.storage.save(data);
			idxsel.init();
			console.log('set form data');
			// console.log(fields);
		}
		var $el, value;
		$.each(fields, function(key, fldname) {
			if(typeof fldname=='object') {
				// console.log('sub-process '+ key);
				value = data[key] ?? {} ;
				exesets.formdata.set(value, fldname);
			}
			else {
				value = data[key] ?? '' ;
				$el = $('[name='+fldname+']');
				switch($el.attr('type')) {
					case 'checkbox':
					// console.log(key + ':[' + fldname+']');
					// console.log(value);
					$el.attr('checked', value ? true : false );
					break;
					
					default:
					$el.val(value);
				}
			}
		});
	}
	
}, // end formdata

cleandata: function(exeset, reload=0) {
	console.log('clean exeset via API');
	// console.log(exeset);
		
	var api = '<?php echo site_url("/api/ma2/exeval");?>/';
	$.get(api, exeset, function(response) {
		// console.log(response);
		try {
			exeset = response['data'] ?? {};
			// console.log(exeset);
			var html = response['html'] ?? false;
			if(!html) throw new Error('No HTML returned');
			exesets.exevals(html, 1);
		}
		catch(errorThrown) { 
			exesets.exevals(errorThrown);
		}
			
		// put clean data into store
		exesets.formdata.set(exeset);
		if(reload) location.reload();
	})
	.fail(function(jqXHR) {
		exesets.exevals('server error');
	});
},

exevals: function(message, message_ok=0) {
	let exekeys = <?php echo json_encode(array_keys($exeset->exercises));?>;
	var htm, warning;

	exekeys.forEach(function(exekey) {
		if(message_ok) {
			htm = message[exekey] ?? '';
			warning = htm ? false : exekey + ' missing in response';
		}
		else {
			warning = message;
		}
		if(warning) {
			htm = '<div class="p-1 alert alert-danger"><ul class="list-unstyled m-0"><li>' + warning + '</li></ul></div>';
		}
		$('#exesitem'+exekey+' .exeval').html(htm);
	});
},

storage: {
	data: function() {
		try {
			var string = localStorage.getItem('mag-exesets');
			var data = JSON.parse(string);
			if(!Array.isArray(data)) data = [];
			return data;
		}
		catch(errorThrown) { 
			console.error('storage: ' + errorThrown);
		}
		return [];
	},
	
	load: function() {
		var data = exesets.storage.data();
		var exeset = data[exesets.idx] ?? {} ;
		console.log('load exeset from local');
		// console.log(exeset);
		// console.log(data);
		return exeset;
	},
	
	save: function(exeset) {
		var data = exesets.storage.data();
		data[exesets.idx] = exeset;
		console.log('store exesets to local');
		// console.log(data);
		localStorage.setItem('mag-exesets', JSON.stringify(data));
	},
	
	add: function(exeset) {
		var data = exesets.storage.data();
		data.push(exeset);
		console.log('add new exeset');
		localStorage.setItem('mag-exesets', JSON.stringify(data));
	},
	
	delete: function() {
		console.log('delete current exeset');
	}
} // end storage
	
};

const idxsel = {
	selector: null,
	init: function() {
		idxsel.selector = $('select[name=idx]');
		idxsel.selector.html('');
		var data = exesets.storage.data();
		data.forEach(function(value, index, array) {
			var optionText = value['name'] ?? '??';
			idxsel.selector.append(new Option(optionText, index));
		});
		idxsel.selector.val(<?php echo $idx;?>);
	},
	reload: function(idx='#') {
		var base_url = '<?php echo base_url("ma2/routine");?>/';
		if(idx==='#') idx = idxsel.selector.val();
		var new_url = base_url + idx;
		window.location.assign(new_url);
	}
}


$(function() {


$('#editform [name=rulesetname]').change(function() {
	var exeset = exesets.formdata.get();
	exesets.cleandata(exeset, 1);
	// $('#editform').submit();
});

document.getElementById('execlear').addEventListener('show.bs.modal', function(event) {
	let exename = $('#exes .nav-tabs .active').html();
	$('#execlear .exename').html(exename);
});

document.getElementById('delentry').addEventListener('show.bs.modal', function(event) {
	let entname = $('#editform [name=name]').val();
	$('#delentry .entname').html(entname);
});

exesets.load();
idxsel.init();

});

function execlear(exekey) {
	$('#exes .tab-pane.active select').val('');
	$('#exes .tab-pane.active input[type=text]').val('');
	$('#exes .tab-pane.active input[type=number]').val(0);
	$('#exes .tab-pane.active input[type=checkbox]').prop("checked", false);
	$('#exes .tab-pane.active .exeval').html('');
}
