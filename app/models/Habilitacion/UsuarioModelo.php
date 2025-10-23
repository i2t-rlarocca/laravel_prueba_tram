<?php
namespace models\Habilitacion;

	class UsuarioModelo extends \Eloquent {
		public $table='basica.usuario_v';
		//public $table='premios.usuario_v';//-->para prueba 70
		public $timestamps = false;
		
		/**
		* Relación con el modelo de provincia
		* Un usuario pertenece a una y solo una provincia
		* $this->belongsTo('nombreModelo','nombre del id en la tabla del modelo','columna a la que hace referencia (foreingKey)');
		*/
		public function provincia(){
			return $this->hasOne('models\Habilitacion\ProvinciaModelo','id','idProvincia');
		}

		/**
		* Relación con el modelo de localidd
		* Un usuario pertenece a una y solo una localidad
		* La relación se hace a través del código postal.
		* $this->belongsTo('nombreModelo','nombre del id en la tabla del modelo','columna a la que hace referencia (foreingKey)');
		*/
		public function localidad(){
			return $this->hasOne('models\Habilitacion\LocalidadModelo','codigo_postal','codigoPostalAgencia');
		}

		public function freshTimestamp(){
			return \Carbon\Carbon::now('America/Argentina/Buenos_Aires');
		}
			
	}

?>