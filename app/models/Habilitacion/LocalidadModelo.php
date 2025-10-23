<?php
	namespace models\Habilitacion;
	
	class LocalidadModelo extends \Eloquent{
		protected $table = 'basica.localidad';
		//protected $table = 'premios.localidad';//-->para prueba 70
		
		/**
		* Relación con el modelo de provincia
		* Una localidad pertenece a una y solo una provincia
		* $this->hasOne/hasMany/belongsTo('nombreModelo','nombre del id en la tabla del modelo','columna a la que hace referencia (foreingKey)');
		*/
		public function provincia(){
			return $this->belongsTo('models\Habilitacion\ProvinciaModelo','idProvincia','id');
		}


		/**
		* Relación con el modelo de Departamento
		* Una localidad pertenece a una y solo una provincia
		* $this->hasOne/hasMany/belongsTo('nombreModelo','nombre del id en la tabla del modelo (en el que estoy parada)','columna a la que hace referencia (foreingKey)');
		*/
		public function departamento(){
			return $this->belongsTo('models\Habilitacion\DepartamentoModelo','id_departamento','id');
		}

		public function freshTimestamp(){
			return \Carbon\Carbon::now('America/Argentina/Buenos_Aires');
		}
		
	}
?>