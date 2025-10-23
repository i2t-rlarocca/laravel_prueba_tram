<?php
namespace models\Habilitacion;
class DependenciaModelo extends \Eloquent {
	protected $table = 'cambio_dependencia';

	public function freshTimestamp(){
		return \Carbon\Carbon::now('America/Argentina/Buenos_Aires');
	}	
}

?>