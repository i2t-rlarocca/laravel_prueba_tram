<?php
namespace controllers;
	use \Dominio\Servicios\Habilitacion\Agencias;
	
	class AgenciaController extends \BaseController{
	
		function __construct() {
			$this->servicio = new Agencias();
		}
		
		//lista de todas las agencias
		public function buscarTodas(){
			$agencias = $this->servicio->buscarTodas();
			return $agencias;
		}
		
			
		/*
			Busca una agencia según el nro_red que llega por parámetro.
			Si agente!=0 && subagente==0 (pasa a ser subagente de nueva red), la red destino
			debe ser != a la red actual.
		*/
		public function buscarPorNumeroRed($nro_red){
			\Log::info('buscaPorNumeroRed - red ini', array($nro_red));
			$agencia = $this->servicio->buscarPorNumeroRed($nro_red);
			\Log::info('buscaPorNumeroRed - red dev', array($nro_red));
			return $agencia;
		}
		
		/***************************************/
		/* Busca el mayor subagente de una red */
		/***************************************/
		public function mayorSubagenteRed($nro_red, $nro_subagente, $modalidad){
			$mayor = $this->servicio->mayorSubagenteRed($nro_red, $nro_subagente, $modalidad);
			return $mayor;
	}

	
	}

?>