<?php
namespace controllers;
	use \Dominio\Servicios\Habilitacion\Provincias;
	
	class ProvinciaController extends \BaseController{
	
		function __construct() {
			$this->servicio = new Provincias();
		}
		
		//lista de todas las provincias
		public function buscarTodas(){
			$provincias = $this->servicio->buscarTodas();
			return $provincias;
		}
		
		/**
		* Devuelve una lista de todas las provincias
		* según los criterios especificados por los parámetros
		*/
		public function buscar($id,$on=false){
			$provincias=$this->servicio->buscar($id,$on);
			return $provincias;
		}
		
		//busca una provincia según el id que llega por parámetro
		public function buscarPorId($id){
			$provincia = $this->servicio->buscarPorId($id);
			return $provincia;
		}
		
		//busca las provincias que no tienen carga por web
		public function buscarOnlineOff(){
			$provincias = $this->servicio->buscarOnlineOff();
			return $provincias;
			
			//print_r($provincias);
		}

		public function departamentos(){
			$departamentos = $this->servicio->departamentos();
			return $departamentos;
		}
	
	}

?>