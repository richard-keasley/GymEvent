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
	$include = VIEWPATH . "help/{$stub}.php";
	# d(VIEWPATH);
	# $config = config('Paths');
	# d($config->viewDirectory);

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
}

}
