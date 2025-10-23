<?php
namespace models\Habilitacion;

	class AgenciaModelo extends \Eloquent {
		public $table='agencias_v';
		protected $primaryKey = 'id_agencia';
		public $timestamps = false;

		public function freshTimestamp(){
			return \Carbon\Carbon::now('America/Argentina/Buenos_Aires');
		}
		
	}

?>