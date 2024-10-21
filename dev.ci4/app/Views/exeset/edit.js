const esedit = {

init: function() {
	// get all modals on this page
	esedit.modals = {};
	let id = null, modalname = null;
	$('.modal').each(function(index) {
		modalname = this.id.substring(4);
		esedit.modals[modalname] = new bootstrap.Modal('#'+this.id);
	});
},

modals: null,
show: function(showname) {
	// prepare modal content
	switch(showname) {
		// ensure it's saved
		case 'view-judge':
		case 'view-default':
		case 'data':
		exesets.update(showname);
		break;
	
		case 'delete':
		var entname = $('#editform [name=name]').val();
		$('#dlg_delete .entname').html(entname);
		break;
	}
	
	for(var modalname in esedit.modals) {
		var modal = esedit.modals[modalname];
		if(modalname===showname) {
			if(!modal._isShown) modal.show();
		}
		else {
			// hide all other modals 
			if(modal._isShown) modal.hide();
		}
	}
},

load: function() {
	var data = <?php echo json_encode($import);?>;
	if(data) {
		exesets.storage.set(data);
		exesets.idxsel.store(0);	
		window.location.assign(exesets.site_url('routine'));
	}
},
	
save: function() {
	var data = exesets.storage.get();
	$('#esedit-save [name=exesets]').val(JSON.stringify(data));
	$('#esedit-save').submit();
	esedit.show('#'); // hides all modals		
},

clear: function() {
	exesets.storage.set([]);
	exesets.idxsel.store(0);	
	window.location.assign(exesets.site_url('routine'));
},

namechange: function(value) {
	exesets.idxsel.selector.find(":selected").text(value);
	exesets.update();
},

selector: {
	// skill selector
	rulesetname: null,
	exekey: '',
	elnum: '',
	dlg: null,
	show: function(rulesetname, exekey, elnum) {
		esedit.selector.elnum = elnum;
		esedit.selector.rulesetname = rulesetname;
		esedit.selector.exekey = exekey;
		
		var dlg_id = ['dlgsel', rulesetname, exekey];
		dlg_id = '#'+dlg_id.join('-');
		esedit.selector.dlg = new bootstrap.Modal(dlg_id);
		esedit.selector.dlg.show();
	},
	apply: function(el) {
		var skillparts = el.dataset.skill.split('|');
		var fldparts = [
			esedit.selector.exekey, 
			'el', 
			esedit.selector.elnum, 
			0
		];
		var fldname = '';
		skillparts.forEach((val, key) => {
			fldparts[3] = key;
			fldname = fldparts.join('_');
			$('[name='+fldname+']').val(val);	
		});
		esedit.selector.dlg.hide();
	}
}

}