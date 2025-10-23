<?php
namespace models\Habilitacion;

	class DetalleTramiteModelo extends \Eloquent {
		public $table='detalle_tramite';
		public $primaryKey='id_detalle';
		public $timestamps = false;
		
		public function freshTimestamp(){
			return \Carbon\Carbon::now('America/Argentina/Buenos_Aires');
		}
	}

?>