<?php
namespace models\Habilitacion;

	class PersonaModelo extends \Eloquent {
		public $table='persona';
		public $timestamps = false;


		/**
		* Relación con el modelo de localidad
		* Una persona pertenece a una localidad
		*/
		public function localidad(){
			return $this->belongsTo('models\Habilitacion\LocalidadModelo','id_localidad','id');
		}

		public function freshTimestamp(){
			return \Carbon\Carbon::now('America/Argentina/Buenos_Aires');
		}
		
	}

?>