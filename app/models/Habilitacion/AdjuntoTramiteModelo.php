<?php
namespace models\Habilitacion;

	class AdjuntoTramiteModelo extends \Eloquent {
		public $table='adjuntos_tramites';

		public function freshTimestamp(){
			return \Carbon\Carbon::now('America/Argentina/Buenos_Aires');
		} 
	
		/**
		* Relación con tipoDocumento $this->belongsTo('modelo', 'local_key', 'parent_key');
		**/		
		public function tipoDocumento()
	    {
	        return $this->belongsTo('models\Habilitacion\TipoDocumentoModelo','id_tipo_doc', 'id');
	    }
		
	}

?>