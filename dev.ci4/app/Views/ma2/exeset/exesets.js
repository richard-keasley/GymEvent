const exesets = {

csrf: {
	token: '<?php echo csrf_token();?>',
	hash: '<?php echo csrf_hash();?>'
},

idx: 0,

tmpl: null,

update: function() {
	var exeset = exesets.formdata.get();
	exesets.formdata.set(exeset);
},

clone: function() {
	var exeset = exesets.formdata.get();
	exesets.storage.save(exeset);
	exeset['name'] = '# new';
	var idx = exesets.storage.add(exeset);
	idxsel.reload(idx);
},

delete: function() {
	exesets.storage.delete();
	idxsel.reload(0);
},

formdata: {
	get: function(fields=null) {
		var formdata = {}, el = null;
		
		if(!fields) {
			fields = exesets.tmpl.fields;
			exesets.log('get form data');
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
	
	set: function(exeset) {
		exesets.log('clean exeset via API');

		exeset[exesets.csrf.token] = exesets.csrf.hash;
		
		var api = '<?php echo site_url("/api/ma2/exeval");?>';
		$.post(api, exeset)
		.done(function(response) {
			try {
				// display cleaned data
				exeset = response['data'] ?? false;
				if(!exeset) throw new Error('No data returned');
				exesets.formdata.htm(exeset);
				
				// display D score info
				var html = response['html'] ?? false;
				if(!html) throw new Error('No HTML returned');
				exesets.exevals(html, 1);
			}
			catch(errorThrown) { 
				exesets.exevals(errorThrown);
			}
		})
		.fail(function(jqXHR) {
			var message = jqXHR['message'] ?? 'server error' ;
			exesets.exevals(message);
			console.error(jqXHR);
		});
	},
		
	htm: function(data, fields=null) {
		if(!fields) {
			exesets.log('write form html');
			var ruleset = data['ruleset'] ?? null;
			exesets.setTemplate(ruleset); // load new template
			
			fields = exesets.tmpl.fields;
			exesets.storage.save(data);
			idxsel.init();
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

printdata: {
	set: function(exeset) {
		exesets.log('clean exeset via API');
		exeset[exesets.csrf.token] = exesets.csrf.hash;
		
		var html = '';
		var api = '<?php echo site_url("/api/ma2/print");?>';
		$.post(api, exeset)
		.done(function(response) {
			try {
				exesets.printdata.msg(response, 1);
			}
			catch(errorThrown) {
				exesets.printdata.msg(errorThrown);
			}
		})
		.fail(function(jqXHR) {
			exesets.printdata.msg('server error');
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
		$('#printdata').html(htm);
	}
},

setTemplate: function(ruleset) {
	var current = exesets.tmpl ?? false;
	if(current) current = current.name ?? false;
	if(current===ruleset.name) return;
	
	exesets.log('load template ' + ruleset.name);
	
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
	$link[0].href = '<?php echo base_url("ma2/rules");?>/'+ruleset.name;
	
	var source = $('#template-'+ruleset.name).html();
	$('#edit-template').html(source);	
	exesets.tmpl = exesets_tmpl[ruleset.name];
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
	get: function() {
		try {
			var item = localStorage.getItem('mag-exesets');
			var data = JSON.parse(item);
			if(!Array.isArray(data)) data = [];
			return data;
		}
		catch(errorThrown) { 
			console.error('storage: ' + errorThrown);
		}
		return [];
	},
	
	set: function(data) {
		localStorage.setItem('mag-exesets', JSON.stringify(data));
	},
	
	load: function(idx='#') {
		if(idx==='#') idx = exesets.idx;
		var data = exesets.storage.get();
		var exeset = data[idx] ?? {} ;
		exesets.log('load exeset from local');
		return exeset;
	},
	
	save: function(exeset) {
		var data = exesets.storage.get();
		data[exesets.idx] = exeset;
		exesets.log('store exesets to local');
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

log: function(message) {
	// console.log(message);
}
	
};
