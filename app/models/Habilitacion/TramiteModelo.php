<?php
namespace models\Habilitacion;

	class TramiteModelo extends \Eloquent {
		public $table='tramites';
		protected $primaryKey='nro_tramite';//$keys=array('nro_tramite', 'id_tipo_tramite', 'nro_permiso', 'id_estado_tramite');
		public $timestamps = false;

		/**
		* Relación con tipotramite	$this->belongsTo('modelo', 'local_key', 'parent_key');
		**/		
		public function tipoTramite()
	    {
	        return $this->belongsTo('models\Habilitacion\TipoTramiteModelo','id_tipo_tramite', 'id_tipo_tramite');
	    }
		
		/**
		* Relación con estadotramite	$this->belongsTo('modelo', 'local_key', 'parent_key');
		**/		
		public function estadoTramite()
	    {
	        return $this->belongsTo('models\Habilitacion\EstadoTramiteModelo','id_estado_tramite', 'id_estado_tramite');
	    }

	    /**
	    * Llamada al storeprocedure para actualizar la información en suitecrm
	    **/
	    public static function storedProcedureCall($usuario) {
	     $conexion=\DB::connection('suitecrm_cas')->statement('call automatizacion(DATE_FORMAT(NOW(), "%Y-%m-%d"),"'.$usuario.'")');
         //\Log::info(\DB::connection('suitecrm_cas')->getQueryLog());
         \DB::disconnect('suitecrm_cas');
         return $conexion;
    	}

    	public function freshTimestamp(){
			return \Carbon\Carbon::now('America/Argentina/Buenos_Aires');
		}
	}

?>