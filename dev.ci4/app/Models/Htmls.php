<?php namespace App\Models;
use CodeIgniter\Model;

class Htmls extends Model {

protected $table      = 'htmls';
protected $primaryKey = 'id';
protected $returnType = 'App\Entities\Html';
protected $updatedField  = 'updated';
protected $allowedFields = ['id', 'path', 'heading', 'value', 'updated'];

protected $validationRules = [
	'id' => 'permit_empty',
	'path' => 'required|is_unique[htmls.path,id,{id}]',
];

function find_path($path=null) {
	if(!$path) {
		$routing = config('Routing');
		$ns = $routing->defaultNamespace;
		$index = $routing->defaultMethod;
		$defcon = DIRECTORY_SEPARATOR . $routing->defaultController;
			
		$router = service('router');
		$controller = $router->controllerName();
		$path = substr($controller, strlen($ns)+2);
		$path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
		if(str_ends_with($path, $defcon)) {
			$pos = strpos($path, $defcon);
			$path = substr($path, 0, $pos);
		}

		$method = $router->methodName();
		if($method!=$index) $path .= DIRECTORY_SEPARATOR . $method;
		$path = strtolower($path);
	}
	# d($controller, $method, $path);
	return $this->where('path', $path)->first();
}

}
