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
	'breadcrumbs' => [['', '<span class="bi-house-fill"></span>']]
];

/**
 * An array of helpers to be loaded automatically upon
 * class instantiation. These helpers will be available
 * to all other controllers that extend BaseController.
 *
 * @var array
 */
protected $helpers = ['inflector', 'json', 'form'];

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
	# d($this->response->getHeaders());
}

protected function download($data, $layout='table', $filetitle='download', $filetype='') {
	$filetype = strtolower($filetype);
	$filetitle = strtolower(preg_replace('#[^A-Z0-9]#i', '_', $filetitle));

	switch($filetype) {
		case 'json':
		$this->response->setJSON($data);
		$response = $this->response->getBody();
		break;
		
		case 'xml':
		$this->response->setXML($data);
		$response = $this->response->getBody();
		// replace data line numbers 
		// CI replaces integer keys with "item{int}"
		$response = preg_replace('#<item[0-9]+>#', '<item>', $response);
		$response = preg_replace('#</item[0-9]+>#', '</item>', $response);
		break;
		
		default:
		$filetype = 'csv';
		$data['format'] = $filetype;
		$response = view("export/{$layout}", $data);
		// remove DEBUG comments from view
		$response = preg_replace('#<!--.*-->[\r\n]#', '', $response);
		/* https://stackoverflow.com/questions/33592518/how-can-i-setting-utf-8-to-csv-file-in-php-codeigniter
		prepend BOM to UTF8 file downloads
		*/
		$response = "\xEF\xBB\xBF" . $response; 
	}
	
	if(false) {
		// view response for debug
		if($filetype=='csv') {
			$this->response->setHeader('content-type', 'text/plain;charset=UTF-8'); 
		}
		return $response;
	}
		
	// send download
	$filename = "{$filetitle}.{$filetype}";
	return $this->response->download($filename, $response);
}

protected function saveplayer($event_id, $name, $html) {
	/* used by 
	App\Controllers\Control\Player
	App\Controllers\Control\Teamtime
	*/
	
	// remove timestamp info
	$html = preg_replace('#\?t=\d+"#', '"', $html);
	// make paths relative and hide footers
	$replacements = [
		[base_url('app/'), 'app/'],
		[base_url("public/events/{$event_id}/music/"), 'music/'],
		['<footer ', '<footer style="display:none;" '],
	];
	foreach($replacements as $replacement) {
		$html = str_replace($replacement[0], $replacement[1], $html);
	}
	
	# return $html; // debug
	return $this->response->download($name, $html);	
}

}
