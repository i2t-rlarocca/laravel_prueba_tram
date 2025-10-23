<?php
namespace models\Habilitacion;
class CategoriaModelo extends \Eloquent {
	protected $table = 'cambio_categoria';
	//protected $table2 = 'age_red';
	//protected $connection='mysql_2';

	public function freshTimestamp(){
		return \Carbon\Carbon::now('America/Argentina/Buenos_Aires');
	}   	
}

?>