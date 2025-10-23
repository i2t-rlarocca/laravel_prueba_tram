<?php
	
	/**
	* Clase de servicio que se comunica con el repositorio
	* para obtener los datos del modelo.
	*/
	
	namespace Dominio\Servicios\Habilitacion;
	use Datos\Repositorio\Habilitacion\TipoDocumentoRepo;
	
	class TipoDocumento{
		
		/**
		* Devuelve una lista de todos los TipoTramites habilitados
		* $funcion: 1-interna 0-agente/subagente(CAS)
		*/
		public static function buscarTodos(){
			$tiposDocumentos=TipoDocumentoRepo::buscarTodos();
			\Log::info('MM - S-TipoDocumento - buscarTodos: ',array($tiposDocumentos));
			$result='';
			foreach ($tiposDocumentos as $tipoDocumento) {
				$result[]=TipoDocumentoRepo::arregloTipoDocumento($tipoDocumento);
			}
			
			return $result;
		}
		
		/**
		* Devuelve un tipo de tramite cuyo id coincide
		* con el pasado por parámetro
		*/
		public function buscarPorId($id){
			$tipoDocumento = TipoDocumentoRepo::buscarPorId($id);
			$tipoDocumento = TipoDocumentoRepo::arregloTipoDocumento($tipoDocumento);
			return $tipoDocumento;
		}

		/**
		* Devuelve el id al buscarlo por el prefijo
		* que viene como parámetro
		*/
		public function buscarPorPrefijo($prefijo){
			$tipoDocumento = TipoDocumentoRepo::buscarPorPrefijo($prefijo);
			// $tipoDocumento = TipoDocumentoRepo::arregloTipoDocumento($tipoDocumento);
			return $tipoDocumento;
		}
		



		// /**
		// * Devuelve una lista de todos los ids TipoTramites habilitados
		// * $funcion:1-interna 0-agente/subagente(CAS)
		// */
		// public static function buscarTodosLosIds($funcion){
			// $tiposDocumentos=TipoDocumentoRepo::buscarTodosLosIds($funcion);
			// return $tiposTramites;
		// }

		

		// /**
		// * Devuelve una lista con las funciones para máquinas
		// **/
		// public function buscarTiposTramitesMaquinas(){
			// $tiposTramites = TipoTramiteRepo::buscarTiposTramitesMaquinas();
			// foreach ($tiposTramites as $tipoTramite) {
				// $result[]=TipoTramiteRepo::arregloTipoTramite($tipoTramite);
			// }
			
			// return $result;
		// }
		// /**
		// * Devuelve un tipo de tramite cuyo nombre coincide
		// * con el pasado por parámetro
		// */
		// public function buscarSimilaresPorNombre($nombre, $listaFunciones){
			// $tipoTramite = TipoTramiteRepo::buscarSimilaresPorNombre($nombre, $listaFunciones);
			// $tipoTramite = TipoTramiteRepo::arregloTipoTramite($tipoTramite);
			// return $tipoTramite;
		// }

		// /**
		// * Devuelve el nombre del tipo de tramite según sea para nuevo permiso o no
		// */
		// public function tituloTramite($id,$alta){
			// $tituloTramite = TipoTramiteRepo::tituloTramite($id,$alta);
			// return $tituloTramite;
		// }


	
	}
?>