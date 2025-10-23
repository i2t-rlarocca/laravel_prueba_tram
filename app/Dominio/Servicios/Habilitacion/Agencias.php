<?php
/**
	* Clase de servicio que se comunica con el repositorio
	* para obtener los datos del modelo.
	*/
	
	namespace Dominio\Servicios\Habilitacion;
	use Datos\Repositorio\Habilitacion\AgenciaRepo;
	
	class Agencias{
	
		/**
		* Devuelve una lista de todas las agencias
		*/
		public static function buscarTodas(){
			$agencias=AgenciaRepo::buscarTodas();
			foreach ($agencias as $agencia) {
				$result[]=AgenciaRepo::arregloAgencia($agencia);
			}
			return $result;
		}
		
			
		/***********************************/
		/* Busca agencias por el nº de red */
		/***********************************/
		public function buscarPorNumeroRed($nro_red){
			$agencia = AgenciaRepo::buscarPorNumeroRed($nro_red);
			if(!is_null($agencia)){
				$agencia = AgenciaRepo::arregloAgencia($agencia);	
			}
			
			return $agencia;
		}

		/***************************************/
		/* Busca el mayor subagente de una red */
		/***************************************/
		public function mayorSubagenteRed($nro_red, $nro_subagente, $modalidad){
			$mayor = AgenciaRepo::mayorSubagenteRed($nro_red, $nro_subagente, $modalidad);
			return $mayor;
	}

	}


?>