<?php
namespace models\Habilitacion;
class DepartamentoModelo extends \Eloquent {

	protected $table = 'basica.departamento';
    public $timestamps = false;

	/**
	* Relación con el modelo de Departamento
	* Una localidad pertenece a una y solo una provincia
	* $this->hasOne/hasMany/belongsTo('nombreModelo','nombre del id en la tabla del modelo (en el que estoy parada)','columna a la que hace referencia (foreingKey)');
	*/
	public function localidades(){
		return $this->hasMany('models\Habilitacion\LocalidadModelo', 'id', 'id_departamento');
	}

	public function freshTimestamp(){
		return \Carbon\Carbon::now('America/Argentina/Buenos_Aires');
	}
    	
}

?>