<?php
namespace App\Controllers;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 *
 * @package CodeIgniter
 */

use CodeIgniter\Controller;

class BaseController extends Controller {

protected $data = [
	'title' => "GymEvent",	
	'heading' => 'GymEvent',
	'messages' => [],
	'body' => '',
	'back_link' => '',
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
public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger) {
	// Do Not Edit This Line
	parent::initController($request, $response, $logger);
	
	// garbage collection
	// https://www.php.net/manual/en/function.session-gc.php
	$gc_file = WRITEPATH . 'php_session_last_gc';
	$gc_period = 1800; // 30 minutes
	if(file_exists($gc_file)) {
		if(filemtime($gc_file) < time() - $gc_period) {
			# d('Garbage collection');
			session_gc();
			touch($gc_file);
		}
	}
	else touch($gc_file);
		
	// look for help file
	$stub = $this->request->uri->getSegments();
	foreach(array_reverse($stub) as $segment) {
		$ok = 1;
		if(is_numeric($segment)) $ok = 0;
		if($segment=='home') $ok = 0;
		if($ok) break;
		array_pop($stub);
	}
	$stub = $stub ? implode('/', $stub) : 'index';
	$include = config('Paths')->viewDirectory . "/help/{$stub}.php";
	$this->data['help'] = file_exists($include) ? $stub : '';
	
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

protected function download($data, $layout='table', $filetitle='download', $filetype='csv') {

	$data['format'] = $filetype;
	$filetitle = strtolower(preg_replace('#[^A-Z0-9]#i', '_', $filetitle));
	$filename = "{$filetitle}.{$filetype}";

	$response = view("export/{$layout}", $data);
	// remove DEBUG comments from view
	$response = preg_replace('#<!--.*-->[\r\n]#', '', $response);
	
	if(0) {
		// view response for debug
		$this->response->setHeader('content-type', 'text/plain;charset=UTF-8'); 
		return $response;
	}
	
	/* https://stackoverflow.com/questions/33592518/how-can-i-setting-utf-8-to-csv-file-in-php-codeigniter
	prepend this to UTF8 file downloads
	*/
	$response = "\xEF\xBB\xBF" . $response; 
	
	// send download
	return $this->response->download($filename, $response);
}

}
