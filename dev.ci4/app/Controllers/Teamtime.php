<?php namespace App\Controllers;
use \App\Libraries\Teamtime as tt_lib;

class Teamtime extends \App\Controllers\BaseController {
	
public function __construct() {
	$this->data['breadcrumbs'][] = 'teamtime';
}
	
public function index() {
	$this->data['title'] = 'Teamtime';
	$this->data['heading'] = 'Teamtime';
	return view('teamtime/index', $this->data);
}

public function info($var_name) {
	$this->data['title'] = $var_name;
	$this->data['heading'] = "Teamtime {$var_name}";
	$this->data['breadcrumbs'][] = "teamtime/info/{$var_name}";
	$this->data['back_link'] = "teamtime";
	$this->data['var_name'] = $var_name;
	
	// ToDo: move this somewhere else
	if($var_name=='music') {
		return view('teamtime/info/music', $this->data);
	}
	
	/* from /app/teamtime/display.css */
	$this->data['head'] = '<style>
	.runtable table {
		border: 1px solid #226;
		margin: 0;
		padding: .3em;
		width: 100%;
		max-width: 30em;
		table-layout: fixed;
		border-collapse: collapse;
	}
	.runtable table td {
		border: 0;
		line-height: 1.2em;
		padding: 0;
		text-align: center;
		overflow: hidden;
	}
	.runtable table tr.omode.active td, 
	.runtable table tr.tmode.active td, 
	.runtable table tr.cmode.active td.active {
		background: #FDE;
	}
	.runtable thead td,
	.runtable thead th {
		font-weight: bold;
	}
	</style>';

	return view('teamtime/info', $this->data);
}

public function display($ds_id=0) {
	$this->data['display'] = tt_lib::get_var('displays', $ds_id);
	$this->data['ds_id'] = $ds_id;
	if(!$this->data['display']) {
		$this->data['message'] = "Invalid display ({$ds_id})";
		return view('teamtime/displays/error', $this->data);
	}
	$get_var = tt_lib::get_var('displays');
	$this->data['ds_updated'] = tt_lib::timestamp($get_var->updated_at);
	$this->data['title'] = $this->data['display']['title'];
	$this->data['style'] = $this->data['display']['style'];
	return view('teamtime/displays/view', $this->data);
}

}