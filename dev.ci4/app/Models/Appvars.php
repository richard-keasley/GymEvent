<?php namespace App\Models;
use CodeIgniter\Model;

class Appvars extends Model {

protected $table      = 'appvars';
protected $primaryKey = 'id';
protected $returnType = 'App\Entities\Appvar';
protected $allowedFields = ['value','id'];

public function get_value($key) {
	$appvar = $this->find($key);
	return $appvar ? $appvar->value : null;
}

public function save_var($appvar) {
	if(empty($appvar->id)) return false;
	// can't use codeigniter "save" because id is always present
	if($this->find($appvar->id)) return $this->save($appvar);
	else return $this->insert($appvar);
}

}
