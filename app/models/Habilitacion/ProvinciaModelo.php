<?php
namespace models\Habilitacion;
class ProvinciaModelo extends \Eloquent {
	protected $table = 'basica.provincia';
	//protected $table = 'premios.provincia';//-->para prueba 70
    protected $timestamp = false;


    	/**
		* Relación con el modelo de localidad
		* Una provincia tiene muchas localidades
		*/
		public function localidades(){
			return $this->hasMany('models\Habilitacion\LocalidadModelo','idProvincia','id');
		}

		public function freshTimestamp(){
			return \Carbon\Carbon::now('America/Argentina/Buenos_Aires');
		}
}

?>