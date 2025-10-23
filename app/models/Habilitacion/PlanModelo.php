<?php
namespace models\Habilitacion;

	class PlanModelo extends \Eloquent {
		public $table='plan_estrategico';
		
		public function freshTimestamp(){
			return \Carbon\Carbon::now('America/Argentina/Buenos_Aires');
		}
		
	}

?>