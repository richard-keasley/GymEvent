<?php namespace App\Libraries;

class Exeset {

public function routine($viewname='', $layout='', $file=null) {
	// helper for controller method 'routine'
	$data['upload'] = null;
	if($file) {
		if($file->isValid()) {
			$json = file_get_contents($file->getPathname());
			$upload = \App\Libraries\Rulesets\Exeset::read_json($json);
			if($upload['error']) {
				$data['messages'] = $upload['error'];
			}
			else {
				$data['upload'] = $upload;
				$data['upload']['file'] = $file;
			}
			
		}
		else {
			$data['messages'] = "Upload: {$file->getErrorString()}";
		}
	}
	
	$config = new \config\paths;
	
	$viewnames = ['edit', 'view'];
	if(!in_array($viewname, $viewnames)) $viewname = 'edit';
	$data['viewname'] = $viewname;
	$include = "{$config->viewDirectory}/exeset/{$viewname}-{$layout}.php";
	$data['layout'] = is_file($include) ? $layout : '' ;
	
	$css = "{$config->viewDirectory}/exeset/{$viewname}.css";
	$minifier = new \MatthiasMullie\Minify\CSS($css);
	$css = "{$config->viewDirectory}/exeset/{$viewname}-{$layout}.css";
	$minifier->add($css);
	$data['style'] = $minifier->minify();
	
	return $data;
}

}
