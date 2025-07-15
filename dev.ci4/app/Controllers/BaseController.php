<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller {

protected $data = [
	'title' => "GymEvent",	
	'heading' => 'GymEvent',
	'messages' => [],
	'body' => '',
	'back_link' => '',
	'showhelp' => true,
	'serviceworker' => true,
	'breadcrumbs' => [['', '<span class="bi-house-fill"></span>']],
	'stylesheets' => ['gymevent.css?v=1'],
];

/**
 * An array of helpers to be loaded automatically upon
 * class instantiation. These helpers will be available
 * to all other controllers that extend BaseController.
 *
 * @var array
 */
protected $helpers = ['inflector', 'number'];

/**
 * Constructor.
 */
public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
	// Do Not Edit This Line
	parent::initController($request, $response, $logger);
		
	// garbage collection
	// https://www.php.net/manual/en/function.session-gc.php
	$gc_file = WRITEPATH . 'php_session_last_gc';
	$gc_period = 7200; // 2 hours
	if(file_exists($gc_file)) {
		if(filemtime($gc_file) < time() - $gc_period) {
			# d('Garbage collection');
			session_gc();
			$count = (new \App\Libraries\Ipinfo)->clean();
			touch($gc_file);
		}
	}
	else touch($gc_file);
		
	// look for back_link
	$back_link = $this->request->getGet('bl');
	if($back_link) $this->data['back_link'] = $back_link;
	
	// add in device name
	$config = config('App');
	$this->data['device'] = $config->device;
	if($this->data['device']) {
		$this->data['heading'] .= " ({$this->data['device']})";
	}
	else {
		$this->data['device'] = 'live website';
	} 
	
	// discourage search engines
	$this->response->setHeader('X-Robots-Tag', ['noindex', 'nofollow']);
}

protected function download($filename, $data) {
	$echo = $this->request->getGet('echo');
	# $echo = true; // good for debug
	try {
		$filename = strtolower(trim($filename));
		$filename = preg_replace('#[^A-Z0-9\.]+#i', '_', $filename);
		
		$extension = (string) pathinfo($filename, PATHINFO_EXTENSION);
		$mimetype = (string) config('Mimes')::guessTypeFromExtension($extension);
		if(!$mimetype) throw new \exception("{$extension} files are not supported");
		
		$ci_format = new \CodeIgniter\Format\Format(config('Format'));
		$formatter = $ci_format->getFormatter($mimetype);
		
		$vartype = gettype($data);
		if($vartype=='object') $vartype = get_class($data);
		switch($vartype) {
			case 'App\Views\Htm\Cattable':
			$data = match($mimetype) {
				'text/csv' => $data->flattened,
				default => $data->tree
			};
			break;
			// default: leave as raw data
		}
		
		$response = $formatter->format($data);
	} 
	catch(\throwable $ex) {
		$echo = true;
		$mimetype = 'text/plain';
		$response = $ex->getMessage();
	}
		
	if($echo) {
		// echo to screen
		$disallowed = ['text/csv'];
		if(in_array($mimetype, $disallowed)) $mimetype = 'text/plain';
		$this->response->setHeader('content-type', "{$mimetype};charset=UTF-8");
		return $response;
	}
	
	/*
	https://stackoverflow.com/questions/33592518/how-can-i-setting-utf-8-to-csv-file-in-php-codeigniter
	prepend BOM to UTF8 file downloads for Excel
	*/
	if($mimetype=='text/csv') {
		$response = "\xEF\xBB\xBF" . $response;
	}
	return $this->response->download($filename, $response);
}

protected function savepage($filename, $html, $event_id=0) {
	/* used by 
	App\Controllers\Control\Player
	App\Controllers\Control\Teamtime
	*/
	
	// remove timestamp info
	$html = preg_replace('#\?t=\d+"#', '"', $html);
	
	// hide elements with class "savepage-hide"
	$pattern = '/<(\w+) .*savepage-hide.*>/U';
	$html = preg_replace($pattern, '<$1 style="display:none">', $html);
	
	// make paths relative
	$replacements = [
		[base_url('app/'), 'app/'],
		[base_url("public/events/{$event_id}/music/"), 'music/'],
	];
	foreach($replacements as $replacement) {
		$html = str_replace($replacement[0], $replacement[1], $html);
	}
		
	# return $html; // debug
	return $this->response->download($filename, $html);	
}

}
