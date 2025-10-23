<?php
namespace models\Habilitacion;

	class TipoTramiteModelo extends \Eloquent {
		public $table='tipo_tramite';
		public $primaryKey='id_tipo_tramite';
		public $timestamps = false;

		/**
		* Relación con tramite	$this->hasMany('modelo', 'foreign_key', 'local_key');
		**/
		public function tramite()
	    {
	        return $this->hasMany('models\Habilitacion\TramiteModelo', 'id_tipo_tramite', 'id_tipo_tramite');
	    }

	    public function freshTimestamp(){
			return \Carbon\Carbon::now('America/Argentina/Buenos_Aires');
		}
		
	}

?>