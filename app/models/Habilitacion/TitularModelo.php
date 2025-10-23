<?php
namespace models\Habilitacion;

	class TitularModelo extends \Eloquent {
		public $table='cambio_titular';

		public function freshTimestamp(){
			return \Carbon\Carbon::now('America/Argentina/Buenos_Aires');
		}
			
	}

?>