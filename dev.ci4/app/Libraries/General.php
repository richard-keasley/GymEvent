<?php namespace App\Libraries;

class General {
	
const skills = [
	'FX' => ['id', 'difficulty', 'group', 'description'],
	'VT' => ['id', 'tariff', 'group', 'description', 'N', 'I', 'A', 'B', 'S', 'G'],
];

const filepath = FCPATH . 'public/general';
static function files() {
	$files = new \CodeIgniter\Files\FileCollection();
	$filepath = self::filepath;
	if(is_dir($filepath)) {
		$files->addDirectory($filepath);
		$files->removePattern('index.*');
	}
	return $files;
}
	
}
