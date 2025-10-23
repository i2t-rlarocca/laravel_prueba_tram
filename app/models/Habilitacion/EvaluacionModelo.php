<?php
namespace models\Habilitacion;

	class EvaluacionModelo extends \Eloquent {
		public $table='evaluacion_domicilio';
		protected $primaryKey = 'nro_tramite';
		public $incrementing = false;
		protected $fillable = array('nro_tramite'); //campos que se pueden asignar masivamente
		
		/*public static function encontarOcrear($nroTramite)
		{
		    $obj = static::find($nroTramite);
		    return $obj ?: static::create(array('nro_tramite' => $nroTramite ));		    
		}*/

		public function freshTimestamp(){
			return \Carbon\Carbon::now('America/Argentina/Buenos_Aires');
		}
		
	}

?>