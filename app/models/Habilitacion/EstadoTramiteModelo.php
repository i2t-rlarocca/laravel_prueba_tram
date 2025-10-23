<?php
namespace models\Habilitacion;

	class EstadoTramiteModelo extends \Eloquent {
		public $table='estado_tramite';
		public $primaryKey='id_estado_tramite';
		public $timestamps = false;

		public function freshTimestamp(){
			return \Carbon\Carbon::now('America/Argentina/Buenos_Aires');
		}

		public function estadosPosibles(){
			return $this->belongsToMany(
				// EstadoTramiteModelo::class,
				'models\Habilitacion\EstadoTramiteModelo',
				'estados_posibles',
				'estado_actual',
				'estado_siguiente'
			)->withPivot('usuario_habilitado');
		}
	}

?>
