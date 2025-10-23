<?php
namespace models\Habilitacion;
class DomicilioModelo extends \Eloquent {
	protected $table = 'cambio_domicilio';
	
    public function freshTimestamp(){
		return \Carbon\Carbon::now('America/Argentina/Buenos_Aires');
	}
	
}

?>