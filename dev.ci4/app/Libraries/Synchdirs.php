<?php namespace App\Libraries;

class Synchdirs {
public $verbose = 0;
private $rootlen = 0;
private $source_root = '';
private $dest_root = '';
private $commit = 0;
private $log = [];

function __construct($source_root, $dest_root) {
	$arr_dst = explode(DIRECTORY_SEPARATOR, $dest_root);
	$arr_src = explode(DIRECTORY_SEPARATOR, $source_root);
	$count = min(count($arr_dst), count($arr_src));
	$root = [];
	for($i=0; $i<$count; $i++) {
		if($arr_dst[$i]!=$arr_src[$i]) break;
		$root[] = $arr_dst[$i];
	}
	$root = implode(DIRECTORY_SEPARATOR, $root);
	
	$this->source_root = $source_root;
	$this->dest_root = $dest_root;
	$this->rootlen = strlen($root); // log trims self::root
}

function run($paths, $commit = 0) {
	$this->commit = $commit;
	$this->log = [];
	foreach($paths as $path) {
		$this->update_dir($path);
	}
	return $this->log;
}

private function update_dir($source_dir, $fullpath=0) {
	if(!$fullpath) $source_dir = $this->source_root . $source_dir;
	$dest_dir = str_replace($this->source_root, $this->dest_root, $source_dir);
	# $this->log[] = '#'.$dest_dir;
	
	if(file_exists($dest_dir)) {
		// delete directories / files no longer in source
		foreach(new \DirectoryIterator($dest_dir) as $fi_dest) {
			if($fi_dest->isDot()) continue;
			$dest_path = $fi_dest->getPathName();
			$source_path = str_replace($this->dest_root, $this->source_root, $dest_path);
			if(!file_exists($source_path)) {
				if($fi_dest->isFile()) {
					$this->update('del-file', $source_path, $dest_path); 
				}
				if($fi_dest->isDir()) {
					$this->update('del-dir', $source_path, $dest_path); 
				}
			}	
		}
	}
	else {
		// create directory if not in destination
		$this->update('mk-dir', $source_dir, $dest_dir); 
	}
	
	foreach(new \DirectoryIterator($source_dir) as $fi_source) {
		if($fi_source->isDot()) continue;
		$source_path = $fi_source->getPathName();
		# $this->log[] = $source_path;
		$dest_path = str_replace($this->source_root, $this->dest_root, $source_path);
		
		if($fi_source->isFile()) {
			// update files in destination
			if(file_exists($dest_path)) {
				$fi_dest = new \SplFileInfo($dest_path);
				$dest_time = $fi_dest->getMTime();
			}
			else $dest_time = 0;
			if($fi_source->getMTime() > $dest_time) {
				$this->update('update', $source_path, $dest_path); 
			}
		}
			
		if($fi_source->isDir()) {
			$this->update_dir($source_path, 1);
		}
	}
}

private function update($cmd, $source, $dest) {
	$this->log[] = sprintf('%s: %s', $cmd, substr($dest, $this->rootlen));
	
	switch($cmd) {
		case 'update': // copy file $source to $dest
			$this->filesys("copy('{$source}', '{$dest}');");
			return;
		case 'mk-dir': // create directory $dest
			$this->filesys("mkdir('{$dest}');");
			return;
		case 'del-file': // delete file $dest
			$this->filesys("unlink('{$dest}');");
			return;
		case 'del-dir': // delete directory $dest
			$this->rmdir($dest);
			return;
	}
	$this->log[] = "Unsupported command ({$cmd})";
}	

private function rmdir($dir) {
    foreach(glob($dir . '/*') as $file) {
        if(is_dir($file)) $this->rmdir($file);
        else $this->filesys("unlink('{$file}');");
    }
    $this->filesys("rmdir('{$dir}');");
}

private function filesys($cmd) {
	// file system changes made here
	try {
		if($this->commit) eval($cmd);
		if($this->verbose) $this->log[] = ' - ' . $cmd;
		return true;
	}
	catch(\Exception $e) {
		$this->log[] = sprintf('<span style="color:#000;background:#FDD">Failed %s - %s</span>', $cmd, $e->getMessage());
	}
	return false;
}
	
}
