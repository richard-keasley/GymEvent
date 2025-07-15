const exesets = {
	
site_url: function(stub=false, url=true) {
	var path = url ?
		'<?php echo site_url($back_link); ?>' :
		'<?php echo $back_link; ?>' ;
	return stub ? path + '/' + stub : path;
},

csrf: {
	token: '<?php echo csrf_token();?>',
	hash: '<?php echo csrf_hash();?>'
},

tmpl: null,

update: function(viewname=null) {
	var exeset = exesets.formdata.get();
	exesets.formdata.set(exeset, viewname);
},

clone: function() {
	var exeset = exesets.formdata.get();
	exesets.storage.save(exeset);
	exeset['name'] = '# new';
	var idx = exesets.storage.add(exeset);
	exesets.idxsel.change(idx);
},

delete: function() {
	exesets.storage.delete();
	exesets.idxsel.change(0);
},

formdata: {
	get: function(fields=null) {
		var formdata = {}, el = null;
		
		if(!fields) {
			exesets.log('get form data');
			fields = exesets.tmpl.fields;
		}
		
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
	
	set: function(exeset, viewname=null) {
		exesets.log('clean form data via API');
		
		var $rulesets = $('#rulesetname');
		var rulesetname = exeset['rulesetname'] ?? 'none' ;
		var valid = $rulesets.find('option[value='+rulesetname+']').length;
		if(!valid) {
			exeset['rulesetname'] = $rulesets.find("option:first").val();
		}
		
		var api = '<?php echo site_url("/api/exeset/exeval");?>';
		$.post(api, securepost(exeset))
		.done(function(response) {
			try {
				// store cleaned data
				exeset = response['data'] ?? false;
				if(!exeset) throw new Error('No data returned');
				exesets.formdata.htm(exeset);
				
				switch(viewname) {
					case 'view-landscape':
					location.href = exesets.site_url('routine/view/landscape');
					break; 
					
					case 'view-portrait':
					location.href = exesets.site_url('routine/view/portrait');
					break; 
					
					default:
					// display D score info
					var html = response['html'] ?? false;
					if(!html) throw new Error('No HTML returned');
					exesets.exevals(html, 1);	
				}
			}
			catch(errorThrown) { 
				exesets.exevals(errorThrown);
			}
		})
		.fail(function(jqXHR) {
			var message = jqXHR['message'] ?? 'server error' ;
			exesets.exevals(message);
			exesets.log(message, 'error');
			exesets.log(jqXHR, 'error');
		});
	},
		
	htm: function(data, fields=null) {
		if(!fields) {
			exesets.log('write form html');
			var ruleset = data['ruleset'] ?? null;
			exesets.setTemplate(ruleset); // load new template
			
			fields = exesets.tmpl.fields;
			exesets.storage.save(data);
			exesets.idxsel.init();
		}
		var $el, value;
		$.each(fields, function(key, fldname) {
			if(typeof fldname=='object') {
				value = data[key] ?? {} ;
				exesets.formdata.htm(value, fldname);
			}
			else {
				value = data[key] ?? '' ;
				$el = $('[name='+fldname+']');
				switch($el.attr('type')) {
					case 'checkbox':
					$el.attr('checked', value ? true : false );
					break;
					
					default:
					$el.val(value);
				}
			}
		});
	}
	
}, // end formdata

viewdata: {
	set: function(exeset, layout='default') {
		exesets.log('clean view data via API');
		exeset['layout'] = layout;
		// console.log(exeset);
		
		var html = '';
		var api = '<?php echo site_url("/api/exeset/view");?>';
		$.post(api, securepost(exeset))
		.done(function(response) {
			try {
				exesets.viewdata.msg(response, 1);
			}
			catch(errorThrown) {
				exesets.viewdata.msg(errorThrown);
			}
		})
		.fail(function(jqXHR) {
			exesets.viewdata.msg('server error');
		});
	},
	
	msg: function(message, message_ok=0) {
		var htm, warning;
		if(message_ok) {
			htm = message ?? '';
			warning = htm ? false : 'No message in response';
		}
		else {
			warning = message;
		}
		if(warning) {
			htm = '<div class="p-1 alert alert-danger"><ul class="list-unstyled m-0"><li>' + warning + '</li></ul></div>';
		}
		$('#viewdata').html(htm);
	}
}, // end viewdata

setTemplate: function(ruleset) {
	var current = exesets.tmpl ?? false;
	if(current) current = current.name ?? false;
	if(current===ruleset.name) return;

	// create help links and info
	var htm = [], val;
	for(var property in ruleset) {
		switch(property) {
			case 'version':
			val = new Date(ruleset[property]);
			val = 'version: ' + val.toLocaleDateString();
			break;
			
			case 'name':
			val = null;
			break;
			
			default:
			val = ruleset[property];
		}
		if(val) htm.push(val);
	}
	$('#help-ruleset').html(htm.join('<br>'));
	var $link = $('#ruleset-link');
	$link[0].href = exesets.site_url('rules/' + ruleset.name);
	
	// load ruleset templates
	let ruleset_name = ruleset.name ?? '#';
	exesets.tmpl = exesets_tmpl[ruleset_name] ?? null;
	if(!exesets.tmpl) {
		// use first template
		for(ruleset_name in exesets_tmpl) {
			exesets.tmpl = exesets_tmpl[ruleset_name];
			break;
		}
	}
	
	exesets.log('load template ' + ruleset_name);
	source = $('#template-'+ruleset_name).html();
	$('#edit-template').html(source);
},

exevals: function(message, message_ok=0) {
	var htm, warning;

	exesets.tmpl.exekeys.forEach(function(exekey) {
		if(message_ok) {
			htm = message[exekey] ?? null;
			if(htm===null) warning = exekey + ' missing in response';
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
	key: 'exesets-<?php echo $back_link;?>',

	get: function() {
		exesets.log('Get storage ' + exesets.storage.key);
		try {
			var retval = [];
			var item = localStorage.getItem(exesets.storage.key);
			var data = JSON.parse(item);
			if(!data) throw new Error('No data found');
			data.forEach(function(value) {
				if(value && (typeof value)=='object') {
					retval.push(value);
				}
			});
		}
		catch(errorThrown) { 
			exesets.log('storage: ' + errorThrown, 'error');
		}
		return retval;
	},
	
	set: function(data) {
		exesets.log('Set storage ' +  exesets.storage.key);
		localStorage.setItem(exesets.storage.key, JSON.stringify(data));
	},
	
	load: function(idx='#') {
		if(idx==='#') idx = exesets.idx;
		var data = exesets.storage.get();
		var exeset = data[idx] ?? {} ;
		return exeset;
	},
	
	save: function(exeset) {
		var data = exesets.storage.get();
		data[exesets.idx] = exeset;
		exesets.storage.set(data);
	},
	
	add: function(exeset) {
		var data = exesets.storage.get();
		data.push(exeset);
		exesets.log('add new exeset');
		exesets.storage.set(data);
		return (data.length - 1);
	},
	
	delete: function() {
		exesets.log('delete current exeset');
		var data = exesets.storage.get();
		data.splice(exesets.idx, 1);
		exesets.storage.set(data);
	}
}, // end storage

idx: 0,

idxsel: {	
	selector: null,

	init: function() {
		exesets.idxsel.selector = $('select[name=idx]');
		exesets.idxsel.selector.html('');
		var data = exesets.storage.get();
		data.forEach(function(value, index, array) {
			var optionText = value['name'] ?? '??';
			exesets.idxsel.selector.append(new Option(optionText, index));
		});

		var idx = exesets.idxsel.store();
		exesets.idxsel.selector.val(idx);
	},
	
	storekey: 'exesets-<?php echo $back_link;?>-idx',
	store: function(idx=null) {
		if(idx===null) { // get value
			idx = localStorage.getItem(exesets.idxsel.storekey) ?? 0;
			return parseInt(idx);
		}
		else { // set value
			idx = parseInt(idx);
			localStorage.setItem(exesets.idxsel.storekey, idx);
			return idx;
		}
	},

	change: function(idx='#') {
		if(idx==='#') idx = parseInt(exesets.idxsel.selector.val());
		var stored = exesets.idxsel.store();
		if(stored===idx) return;
		exesets.idxsel.store(idx);
		window.location.reload();
	}
}, // end idxsel


log: function(message, type='log') {
	// enable this for debug
	
	switch(type) {
		case 'error': console.error(message); break;
		default: console.log(message);
	}
	// */
}
	
};
