<?php
	namespace models\Habilitacion;
	
	class HistoricoTramiteEstadosModelo extends \Eloquent{
		protected $table = 'hist_tramites_estados';
		public $timestamps = false;
		protected $primaryKey = 'nrotramite';
		
		public function freshTimestamp(){
			return \Carbon\Carbon::now('America/Argentina/Buenos_Aires');
		}
		
	}
?>