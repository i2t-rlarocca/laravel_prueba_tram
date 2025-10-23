<?php
namespace models\Habilitacion;

	class TipoDocumentoModelo extends \Eloquent {
		public $table='tipo_documento';
		// public $primaryKey='id';
		// public $timestamps = false;

		/**
		* Relación con tramite	$this->hasMany('modelo', 'foreign_key', 'local_key');
		**/
		
		// public function adjunto()
	    // {
	        // return $this->hasMany('models\Habilitacion\AdjuntoTramiteModelo', 'id_tipo_doc', 'id_tipo_doc');
	    // }

	    public function freshTimestamp(){
			return \Carbon\Carbon::now('America/Argentina/Buenos_Aires');
		}
		
	}

?>